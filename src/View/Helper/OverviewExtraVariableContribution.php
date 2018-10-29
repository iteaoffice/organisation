<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Organisation\Entity\OParent;
use Program\Entity\Program;

/**
 * Class OverviewExtraVariableContribution
 *
 * @package Organisation\View\Helper
 */
class OverviewExtraVariableContribution extends AbstractViewHelper
{
    public function __invoke(OParent $parent, Program $program, int $year): string
    {
        $invoiceMethod = $this->invoiceService->findInvoiceMethod($program);

        return $this->getRenderer()->render(
            'organisation/partial/overview-extra-variable-contribution',
            [
                'year'               => $year,
                'parent'             => $parent,
                'contactService'     => $this->contactService,
                'versionService'     => $this->versionService,
                'parentService'      => $this->parentService,
                'affiliationService' => $this->affiliationService,
                'projectService'     => $this->projectService,
                'financialContact'   => $this->parentService->getFinancialContact($parent),
                'projects'           => $this->projectService->findProjectsByParent($parent, $program),
                'invoiceFactor'      => $this->parentService->parseInvoiceFactor($parent, $program),
                'invoiceMethod'      => $invoiceMethod,
                'program'            => $program,
            ]
        );
    }
}
