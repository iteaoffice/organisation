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

use Affiliation\Service\AffiliationService;
use Contact\Service\ContactService;
use Invoice\Service\InvoiceService;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Organisation\Entity\OParent;
use Organisation\Options\ModuleOptions;
use Organisation\Service\ParentService;
use Program\Entity\Program;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use ZfcTwig\View\TwigRenderer;

/**
 * Class RenderOverviewVariableContributionSheet
 *
 * @package Organisation\Controller\Plugin
 */
final class RenderOverviewVariableContributionSheet extends AbstractPlugin
{
    private ParentService $parentService;
    private InvoiceService $invoiceService;
    private ModuleOptions $moduleOptions;
    private ProjectService $projectService;
    private VersionService $versionService;
    private ContactService $contactService;
    private AffiliationService $affiliationService;
    private TwigRenderer $renderer;

    public function __construct(
        ParentService $parentService,
        InvoiceService $invoiceService,
        ModuleOptions $moduleOptions,
        ProjectService $projectService,
        VersionService $versionService,
        ContactService $contactService,
        AffiliationService $affiliationService,
        TwigRenderer $renderer
    ) {
        $this->parentService      = $parentService;
        $this->invoiceService     = $invoiceService;
        $this->moduleOptions      = $moduleOptions;
        $this->projectService     = $projectService;
        $this->versionService     = $versionService;
        $this->contactService     = $contactService;
        $this->affiliationService = $affiliationService;
        $this->renderer           = $renderer;
    }

    public function __invoke(OParent $parent, Program $program, int $year): OrganisationPdf
    {
        $pdf = new OrganisationPdf();
        $pdf->setTemplate($this->moduleOptions->getOverviewVariableContributionTemplate());
        $pdf->AddPage();
        $pdf->SetFontSize(8);

        $projects      = $this->parentService->renderProjectsByParentInYear($parent, $program, $year);
        $invoiceMethod = $this->invoiceService->findInvoiceMethod($program);

        $content = $this->renderer->render(
            'organisation/pdf/overview-variable-contribution',
            [
                'year'               => $year,
                'parent'             => $parent,
                'program'            => $program,
                'membershipFactor'   => $this->parentService->parseMembershipFactor($parent),
                'contactService'     => $this->contactService,
                'versionService'     => $this->versionService,
                'parentService'      => $this->parentService,
                'invoiceFactor'      => $this->parentService->parseInvoiceFactor($parent, $program),
                'affiliationService' => $this->affiliationService,
                'projectService'     => $this->projectService,
                'invoiceMethod'      => $invoiceMethod,
                'financialContact'   => $this->parentService->getFinancialContact($parent),
                'projects'           => $projects,
            ]
        );

        $pdf->writeHTMLCell(0, 0, 5, 30, $content);

        return $pdf;
    }
}
