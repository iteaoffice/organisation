<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Organisation
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */

namespace Organisation\View\Helper;

use Zend\View\HelperPluginManager;
use Zend\View\Helper\AbstractHelper;

use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;

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
        $this->organisationService = $helperPluginManager->getServiceLocator()->get('organisation_organisation_service');
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
