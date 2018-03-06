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

namespace Organisation\Controller\Plugin;

use Organisation\Entity\OParent;
use Program\Entity\Program;

/**
 * Class RenderOverviewExtraVariableContributionSheet
 * @package Parent\Controller\Plugin
 */
class RenderOverviewExtraVariableContributionSheet extends AbstractOrganisationPlugin
{
    /**
     * @param OParent $parent
     * @param Program $program
     * @param int $year
     * @return OrganisationPdf
     */
    public function __invoke(OParent $parent, Program $program, int $year): OrganisationPdf
    {
        /**
         * @var $pdf OrganisationPdf|\TCPDF
         */
        $pdf = new OrganisationPdf();
        $pdf->setTemplate($this->getModuleOptions()->getOverviewVariableContributionTemplate());
        $pdf->AddPage();
        $pdf->SetMargins(10, 30, 10, true);
        $pdf->SetFontSize(8);

        $content = $this->getTwigRenderer()->render(
            'organisation/pdf/overview-extra-variable-contribution',
            [
                'year'               => $year,
                'parent'             => $parent,
                'contactService'     => $this->getContactService(),
                'versionService'     => $this->getVersionService(),
                'parentService'      => $this->getParentService(),
                'affiliationService' => $this->getAffiliationService(),
                'projectService'     => $this->getProjectService(),
                'program'            => $program,
                'financialContact'   => $this->getParentService()->getFinancialContact($parent),
                'projects'           => $this->getProjectService()->findProjectsByParent($parent, $program),
                'invoiceFactor'      => $this->getParentService()->parseInvoiceFactor($parent, $program),

            ]
        );

        $pdf->writeHTMLCell(0, 600, 10, 25, $content);

        return $pdf;
    }
}
