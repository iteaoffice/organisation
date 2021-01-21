<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\Parent;

use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use Invoice\Service\InvoiceService;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;
use Program\Service\ProgramService;

use function strlen;

/**
 * Class ParentController
 * @package Organisation\Controller
 */
final class ContributionController extends AbstractController
{
    private ParentService $parentService;
    private ProgramService $programService;
    private InvoiceService $invoiceService;

    public function __construct(
        ParentService $parentService,
        ProgramService $programService,
        InvoiceService $invoiceService
    ) {
        $this->parentService  = $parentService;
        $this->programService = $programService;
        $this->invoiceService = $invoiceService;
    }

    public function overviewVariableContributionAction(): ViewModel
    {
        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $program = $this->programService->findProgramById((int)$this->params('program'));

        if (null === $program) {
            return $this->notFoundAction();
        }

        $year = (int)$this->params('year');

        $invoiceMethod = $this->invoiceService->findInvoiceMethod($program);

        return new ViewModel(
            [
                'year'          => $year,
                'parent'        => $parent,
                'program'       => $program,
                'invoiceMethod' => $invoiceMethod,
                'invoiceFactor' => $this->parentService->parseInvoiceFactor($parent, $program)

            ]
        );
    }

    public function overviewVariableContributionPdfAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        $program = $this->programService->findProgramById((int)$this->params('program'));

        if (null === $program) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        $year = (int)$this->params('year');

        $renderPaymentSheet = $this->renderOverviewVariableContributionSheet($parent, $program, $year);

        $response->getHeaders()->addHeaderLine(
            'Content-Disposition',
            'attachment; filename="' .
            sprintf(
                '%s Estimated Variable Contribution ECSEL_%s.pdf',
                $year,
                $parent->getOrganisation()->getOrganisation()
            ) . '"'
        )
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', strlen($renderPaymentSheet->getPDFData()));
        $response->setContent($renderPaymentSheet->getPDFData());

        return $response;
    }

    public function overviewExtraVariableContributionAction(): ViewModel
    {
        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $program = $this->programService->findProgramById((int)$this->params('program'));

        if (null === $program) {
            return $this->notFoundAction();
        }

        $year = (int)$this->params('year');

        return new ViewModel(
            [
                'year'    => $year,
                'parent'  => $parent,
                'program' => $program

            ]
        );
    }

    public function overviewExtraVariableContributionPdfAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        $program = $this->programService->findProgramById((int)$this->params('program'));

        if (null === $program) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        $year = (int)$this->params('year');

        $renderPaymentSheet = $this->renderOverviewExtraVariableContributionSheet($parent, $program, $year);

        $response->getHeaders()->addHeaderLine(
            'Content-Disposition',
            'attachment; filename="' .
            sprintf(
                '%s Estimated Extra Variable Contribution ECSEL_%s.pdf',
                $year,
                $parent->getOrganisation()->getOrganisation()
            ) . '"'
        )
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', strlen($renderPaymentSheet->getPDFData()));
        $response->setContent($renderPaymentSheet->getPDFData());

        return $response;
    }
}
