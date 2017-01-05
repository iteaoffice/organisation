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

namespace Organisation\View\Helper;

use Organisation\Entity\OParent;

/**
 * Class OverviewVariableContribution
 *
 * @package Parent\View\Helper
 */
class OverviewVariableContribution extends AbstractViewHelper
{
    /**
     * @param OParent $parent
     * @param int     $year
     * @param int     $period
     *
     * @return string
     */
    public function __invoke(OParent $parent, $year, $period): string
    {
        return $this->getRenderer()->render(
            'parent/partial/overview-variable-contribution',
            [
                'year'               => $year,
                'period'             => $period,
                'parent'             => $parent,
                'contactService'     => $this->getContactService(),
                'versionService'     => $this->getVersionService(),
                'parentService'      => $this->getParentService(),
                'invoiceFactor'      => $this->getParentService()->parseInvoiceFactor($parent, $year),
                'affiliationService' => $this->getAffiliationService(),
                'projectService'     => $this->getProjectService(),
                'financialContact'   => $this->getParentService()->getFinancialContact($parent),
                'affiliations'       => $this->getAffiliationService()->findAffiliationByParentAndWhich($parent),
            ]
        );
    }
}
