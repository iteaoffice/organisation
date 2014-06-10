<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Organisation\View\Helper;

use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;

/**
 * Class OrganisationHandler
 * @package Organisation\View\Helper
 */
class OrganisationServiceProxy extends AbstractHelper
{
    /**
     * @var OrganisationService
     */
    protected $organisationService;

    /**
     * @param HelperPluginManager $helperPluginManager
     */
    public function __construct(HelperPluginManager $helperPluginManager)
    {
        $this->organisationService = clone $helperPluginManager->getServiceLocator()->get(
            'organisation_organisation_service'
        );
    }

    /**
     * @param Organisation $organisation
     *
     * @return OrganisationService
     */
    public function __invoke(Organisation $organisation)
    {
        return $this->organisationService->setOrganisation($organisation);
    }
}
