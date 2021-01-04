<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Affiliation\Service\AffiliationService;
use Contact\Service\ContactService;
use Invoice\Service\InvoiceService;
use Organisation\Entity\OParent;
use Organisation\Service\ParentService;
use Program\Entity\Program;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use ZfcTwig\View\TwigRenderer;

class OverviewExtraVariableContribution
{
    private TwigRenderer $renderer;
    private ProjectService $projectService;
    private VersionService $versionService;
    private AffiliationService $affiliationService;
    private InvoiceService $invoiceService;
    private ParentService $parentService;
    private ContactService $contactService;

    public function __construct(TwigRenderer $renderer, ProjectService $projectService, VersionService $versionService, AffiliationService $affiliationService, InvoiceService $invoiceService, ParentService $parentService, ContactService $contactService)
    {
        $this->renderer = $renderer;
        $this->projectService = $projectService;
        $this->versionService = $versionService;
        $this->affiliationService = $affiliationService;
        $this->invoiceService = $invoiceService;
        $this->parentService = $parentService;
        $this->contactService = $contactService;
    }

    public function __invoke(OParent $parent, Program $program, int $year): string
    {
        $invoiceMethod = $this->invoiceService->findInvoiceMethod($program);

        return $this->renderer->render(
            'organisation/partial/overview-extra-variable-contribution',
            [
                'year' => $year,
                'parent' => $parent,
                'contactService' => $this->contactService,
                'versionService' => $this->versionService,
                'parentService' => $this->parentService,
                'affiliationService' => $this->affiliationService,
                'projectService' => $this->projectService,
                'financialContact' => $this->parentService->getFinancialContact($parent),
                'projects' => $this->projectService->findProjectsByParent(
                    $parent,
                    $program,
                    AffiliationService::WHICH_INVOICING,
                    $year
                ),
                'invoiceFactor' => $this->parentService->parseInvoiceFactor($parent, $program),
                'invoiceMethod' => $invoiceMethod,
                'program' => $program,
            ]
        );
    }
}
