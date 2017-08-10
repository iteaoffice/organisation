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

/**
 * Class RenderOverviewVariableContributionSheet
 * @package Organisation\Controller\Plugin
 */
class RenderOverviewVariableContributionSheet extends AbstractOrganisationPlugin
{
    /**
     * @param OParent $parent
     * @param int $year
     * @return OrganisationPdf
     */
    public function __invoke(OParent $parent, int $year): OrganisationPdf
    {
        /**
         * @var $pdf OrganisationPdf|\TCPDF
         */
        $pdf = new OrganisationPdf();
        $pdf->setTemplate($this->getModuleOptions()->getOverviewVariableContributionTemplate());
        $pdf->AddPage();
        $pdf->SetFontSize(8);


        $projects = $this->getParentService()->renderProjectsByParentInYear($parent, $year);


        $content = $this->getTwigRenderer()->render(
            'organisation/pdf/overview-variable-contribution',
            [
                'year'               => $year,
                'parent'             => $parent,
                'membershipFactor'   => $this->getParentService()->parseMembershipFactor($parent),
                'contactService'     => $this->getContactService(),
                'versionService'     => $this->getVersionService(),
                'parentService'      => $this->getParentService(),
                'invoiceFactor'      => $this->getParentService()->parseInvoiceFactor($parent, $year),
                'affiliationService' => $this->getAffiliationService(),
                'projectService'     => $this->getProjectService(),
                'financialContact'   => $this->getParentService()->getFinancialContact($parent),
                'projects'           => $projects,
            ]
        );

        $pdf->writeHTMLCell(0, 0, 5, 30, $content);

        return $pdf;
    }
}
