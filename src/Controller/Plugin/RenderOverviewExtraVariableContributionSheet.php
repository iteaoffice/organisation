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
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\Controller\Plugin;

use Organisation\Entity\OParent;

/**
 * Class RenderOverviewExtraVariableContributionSheet
 * @package Parent\Controller\Plugin
 */
class RenderOverviewExtraVariableContributionSheet extends AbstractOrganisationPlugin
{
    /**
     * @param OParent $parent
     * @param $year
     * @param $period
     * @return OrganisationPdf|\TCPDF
     */
    public function __invoke(OParent $parent, $year, $period): OrganisationPdf
    {
        /**
         * @var $pdf OrganisationPdf|\TCPDF
         */
        $pdf = new OrganisationPdf();
        $pdf->setTemplate($this->getModuleOptions()->getOverviewVariableContributionTemplate());
        $pdf->AddPage();
        $pdf->SetMargins(10, 40, 10, true);
        $pdf->SetFontSize(10);

        $content = $this->getTwigRenderer()->render(
            'organisation/pdf/overview-extra-variable-contribution',
            [
                'year'               => $year,
                'period'             => $period,
                'parent'             => $parent,
                'contactService'     => $this->getContactService(),
                'versionService'     => $this->getVersionService(),
                'parentService'      => $this->getParentService(),
                'affiliationService' => $this->getAffiliationService(),
                'projectService'     => $this->getProjectService(),
                'financialContact'   => $this->getParentService()->getFinancialContact($parent),
                'projects'           => $this->getProjectService()->findProjectsByParent($parent),
                'invoiceFactor'      => $this->getParentService()->parseInvoiceFactor($parent, $year),

            ]
        );

        $pdf->writeHTMLCell(0, 0, 10, 40, $content);

        return $pdf;
    }
}
