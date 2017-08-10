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

/**
 * Class OverviewVariableContribution
 *
 * @package Parent\View\Helper
 */
class OverviewVariableContribution extends AbstractViewHelper
{
    /**
     * @param OParent $parent
     * @param int $year
     * @return string
     */
    public function __invoke(OParent $parent, int $year): string
    {
        return $this->getRenderer()->render(
            'organisation/partial/overview-variable-contribution',
            [
                'year'               => $year,
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
