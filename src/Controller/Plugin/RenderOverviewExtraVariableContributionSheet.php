<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin;

use Affiliation\Service\AffiliationService;
use Contact\Service\ContactService;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Organisation\Entity\ParentEntity;
use Organisation\Options\ModuleOptions;
use Organisation\Service\ParentService;
use Program\Entity\Program;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use ZfcTwig\View\TwigRenderer;

/**
 * Class RenderOverviewExtraVariableContributionSheet
 *
 * @package Organisation\Controller\Plugin
 */
final class RenderOverviewExtraVariableContributionSheet extends AbstractPlugin
{
    private ParentService $parentService;
    private ModuleOptions $moduleOptions;
    private ProjectService $projectService;
    private VersionService $versionService;
    private ContactService $contactService;
    private AffiliationService $affiliationService;
    private TwigRenderer $renderer;

    public function __construct(
        ParentService $parentService,
        ModuleOptions $moduleOptions,
        ProjectService $projectService,
        VersionService $versionService,
        ContactService $contactService,
        AffiliationService $affiliationService,
        TwigRenderer $renderer
    ) {
        $this->parentService      = $parentService;
        $this->moduleOptions      = $moduleOptions;
        $this->projectService     = $projectService;
        $this->versionService     = $versionService;
        $this->contactService     = $contactService;
        $this->affiliationService = $affiliationService;
        $this->renderer           = $renderer;
    }

    public function __invoke(ParentEntity $parent, Program $program, int $year): OrganisationPdf
    {
        $pdf = new OrganisationPdf();
        $pdf->setTemplate($this->moduleOptions->getOverviewVariableContributionTemplate());

        $pdf->SetMargins(10, 30, -1, true);
        $pdf->SetAutoPageBreak(true, 30);
        $pdf->AddPage();
        $pdf->SetFontSize(8);

        $content = $this->renderer->render(
            'organisation/pdf/overview-extra-variable-contribution',
            [
                'year'                => $year,
                'parent'              => $parent,
                'contactService'      => $this->contactService,
                'versionService'      => $this->versionService,
                'parentService'       => $this->parentService,
                'affiliationService'  => $this->affiliationService,
                'projectService'      => $this->projectService,
                'program'             => $program,
                'financialContact'    => $this->parentService->getFinancialContact($parent),
                'projects'            => $this->projectService->findProjectsByParent(
                    $parent,
                    $program,
                    AffiliationService::WHICH_INVOICING,
                    $year
                ),
                'membershipDate'      => sprintf('31-12-%s', $year - 1),
                'amountOfMemberships' => $this->parentService->parseMembershipFactor($parent),
                'invoiceFactor'       => $this->parentService->parseInvoiceFactor($parent, $program),
            ]
        );

        $pdf->writeHTMLCell(0, 0, '', '', $content);

        return $pdf;
    }
}
