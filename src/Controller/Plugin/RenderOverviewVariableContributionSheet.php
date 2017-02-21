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
 * Class RenderOverviewVariableContributionSheet
 * @package Organisation\Controller\Plugin
 */
class RenderOverviewVariableContributionSheet extends AbstractOrganisationPlugin
{
    /**
     * @param OParent $parent
     * @param $year
     * @param $period
     * @return OrganisationPdf
     */
    public function __invoke(OParent $parent, $year, $period): OrganisationPdf
    {
        /**
         * @var $pdf OrganisationPdf|\TCPDF
         */
        $pdf = new OrganisationPdf();
        $pdf->setTemplate($this->getModuleOptions()->getOverviewVariableContributionTemplate());
        $pdf->AddPage();
        $pdf->SetFontSize(8);


        //Sort the projects per call
        $projects = [];
        foreach ($this->getAffiliationService()->findAffiliationByParentAndWhich($parent) as $affiliation) {
            $call = $affiliation->getProject()->getCall();
            //Initialize the array
            if (!array_key_exists($call->getId(), $projects)) {
                $projects[$call->getId()]['affiliation'] = [];
                $projects[$call->getId()]['call'] = $call;
                $projects[$call->getId()]['totalFunding'] = 0;
                $projects[$call->getId()]['totalContribution'] = 0;
            }

            $latestVersion = $this->getProjectService()->getLatestProjectVersion(
                $affiliation->getProject(),
                null,
                null,
                false,
                false
            );

            $funding = $this->getVersionService()->findTotalFundingEuVersionByAffiliationAndVersion(
                $affiliation,
                $latestVersion
            );
            $contribution = $this->getAffiliationService()->parseContribution(
                $affiliation,
                $latestVersion,
                $year,
                $period
            );

            $projects[$call->getId()]['affiliation'][] = [
                'affiliation'  => $affiliation,
                'funding'      => $funding,
                'contribution' => $contribution
            ];


            $projects[$call->getId()]['totalFunding'] += $funding;
            $projects[$call->getId()]['totalContribution'] += $contribution;
        }

        $content = $this->getTwigRenderer()->render(
            'organisation/pdf/overview-variable-contribution',
            [
                'year'               => $year,
                'period'             => $period,
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
