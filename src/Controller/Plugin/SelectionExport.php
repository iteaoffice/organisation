<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Invoice
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/invoice for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin;

use Laminas\Http\Headers;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Organisation\Entity\Organisation;
use Organisation\Entity\Selection;
use Organisation\Service\SelectionService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use function strlen;

/**
 * Class SelectionExport
 *
 * @package Organisation\Controller\Plugin
 */
final class SelectionExport extends AbstractPlugin
{
    public const EXPORT_CSV   = 1;
    public const EXPORT_EXCEL = 2;

    private Spreadsheet $excel;
    private string $csv;
    private SelectionService $selectionService;

    private TranslatorInterface $translator;
    private int $type = self::EXPORT_CSV;
    private Selection $selection;

    public function __construct(SelectionService $selectionService, TranslatorInterface $translator)
    {
        $this->selectionService = $selectionService;
        $this->translator       = $translator;
    }

    public function __invoke(Selection $selection, int $type): SelectionExport
    {
        $this->type      = $type;
        $this->selection = $selection;

        switch ($this->type) {
            case self::EXPORT_CSV:
                $this->exportCSV();
                break;
            case self::EXPORT_EXCEL:
                $this->exportExcel();
                break;
        }

        return $this;
    }

    public function exportCSV(): SelectionExport
    {
        // Open the output stream
        $fh = fopen('php://output', 'wb');

        ob_start();

        fputcsv(
            $fh,
            [
                'id',
                'Organisation',
                'Type',
                'Country'
            ]
        );

        foreach ($this->selectionService->findOrganisationsInSelection($this->selection, true) as $organisation) {
            fputcsv(
                $fh,
                [
                    $organisation['id'],
                    $organisation['organisation'],
                    $organisation['type']['type'],
                    $organisation['country']['iso3'] ?? null,
                ]
            );
        }

        $string = ob_get_clean();

        // Convert to UTF-16LE
        $string = mb_convert_encoding($string, 'UTF-16LE', 'UTF-8');

        // Prepend BOM
        $string = "\xFF\xFE" . $string;

        $this->csv = $string;

        return $this;
    }

    public function exportExcel(): SelectionExport
    {
        $this->excel = new Spreadsheet();

        $exportSheet = $this->excel->getActiveSheet();
        $exportSheet->setTitle($this->translator->translate('txt-selection-export'));
        $exportSheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $exportSheet->getPageSetup()->setFitToWidth(1);
        $exportSheet->getPageSetup()->setFitToHeight(0);

        $header = [
            'Organisation',
            'Type',
            'Country',
            'Country (iso3)',
        ];

        //Create the header row
        $column = 'A';
        foreach ($header as $item) {
            $exportSheet->setCellValue($column . '1', $item);
            $column++;
        }

        $row = 2;

        /** @var Organisation $organisation */
        foreach ($this->selectionService->findOrganisationsInSelection($this->selection) as $organisation) {
            $country = $organisation->getCountry();

            $organisationRow
                = [
                $organisation->getOrganisation(),
                $organisation->getType()->getType(),
                null === $country ? '' : $country->getCountry(),
                null === $country ? '' : $country->getIso3(),
            ];

            $column = 'A';
            foreach ($organisationRow as $item) {
                $exportSheet->setCellValue($column . $row, $item);

                $column++;
            }

            $row++;
        }

        return $this;
    }

    public function translate(string $translate): string
    {
        return $this->translator->translate($translate);
    }

    public function parseResponse(): Response
    {
        switch ($this->type) {
            case self::EXPORT_CSV:
                return $this->parseCsvResponse();
                break;

            case self::EXPORT_EXCEL:
            default:
                return $this->parseExcelResponse();
        }
    }

    public function parseCsvResponse(): Response
    {
        $response = new Response();

        // Prepare the response
        $response->setContent($this->csv);
        $response->setStatusCode(Response::STATUS_CODE_200);
        $headers = new Headers();
        $headers->addHeaders(
            [
                'Content-Disposition' => 'attachment; filename="Export ' . $this->selection->getSelection() . '.csv"',
                'Content-Type'        => 'text/csv',
                'Content-Length'      => strlen($this->csv),
                'Expires'             => '@0', // @0, because ZF2 parses date as string to \DateTime() object
                'Cache-Control'       => 'must-revalidate',
                'Pragma'              => 'public',
            ]
        );
        $response->setHeaders($headers);

        return $response;
    }

    public function parseExcelResponse(): Response
    {
        $response = new Response();
        if (! ($this->excel instanceof Spreadsheet)) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        /** @var Xlsx $writer */
        $writer = IOFactory::createWriter($this->excel, 'Xlsx');

        ob_start();
        $gzip = false;
        // Gzip the output when possible. @see http://php.net/manual/en/function.ob-gzhandler.php
        if (ob_start('ob_gzhandler')) {
            $gzip = true;
        }
        $writer->save('php://output');
        if ($gzip) {
            ob_end_flush(); // Flush the gzipped buffer into the main buffer
        }
        $contentLength = ob_get_length();

        // Prepare the response
        $response->setContent(ob_get_clean());
        $response->setStatusCode(Response::STATUS_CODE_200);
        $headers = new Headers();
        $headers->addHeaders(
            [
                'Content-Disposition' => 'attachment; filename="Export ' . $this->selection->getSelection() . '.xlsx"',
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Length'      => $contentLength,
                'Expires'             => '@0', // @0, because ZF2 parses date as string to \DateTime() object
                'Cache-Control'       => 'must-revalidate',
                'Pragma'              => 'public',
            ]
        );
        if ($gzip) {
            $headers->addHeaders(['Content-Encoding' => 'gzip']);
        }
        $response->setHeaders($headers);

        return $response;
    }
}
