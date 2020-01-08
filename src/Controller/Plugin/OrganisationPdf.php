<?php

/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin;

use setasign\Fpdi\TcpdfFpdi;

/**
 * Class PDF.
 */
final class OrganisationPdf extends TcpdfFpdi
{
    /**
     * 'Remembers' the template id of the imported page.
     */
    protected $_tplIdx;
    /**
     * Location of the template.
     *
     * @var string
     */
    protected $template;

    public function header(): void
    {
        if (null === $this->_tplIdx) {
            if (! file_exists($this->template)) {
                throw new \InvalidArgumentException(sprintf('Template %s cannot be found', $this->template));
            }
            $this->setSourceFile($this->template);
            $this->_tplIdx = $this->importPage(1);
        }

        $this->useTemplate($this->_tplIdx, 0, 0);
        $this->SetFont('freesans', 'N', 15);
        $this->SetTextColor(0);
        $this->SetXY(PDF_MARGIN_LEFT, 5);
    }

    public function footer(): void
    {
        // emtpy method body
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function coloredTable(array $header, array $data, array $width = null, $lastRow = false): void
    {
        // Colors, line width and bold font
        $this->SetDrawColor(205, 205, 205);
        $this->SetFillColor(255, 255, 255);
        $this->SetLineWidth(0.1);
        $this->SetFont('', 'B');
        // Header
        if (null === $width) {
            $w = [40, 35, 40, 45, 40];
        } else {
            $w = $width;
        }

        $num_headers = count($header);

        for ($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'l', 1);
        }


        if ($num_headers === 0) {
            $this->Cell(array_sum($w), 0, '', 'B');
        }

        $this->Ln();


        // Color and font restoration
        $this->SetFillColor(249, 249, 249);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = 0;
        $rowCounter = 1;
        foreach ($data as $row) {
            $counter = 0;

            foreach ($row as $column) {
                if ($lastRow && $rowCounter === \count($data)) {
                    $this->SetFont('', 'B');
                }


                $this->Cell($w[$counter], 6, $column, 'LR', 0, 'L', $fill);
                $counter++;
            }
            $rowCounter++;
            $this->Ln();
            $fill = ! $fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln();
    }
}
