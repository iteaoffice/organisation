<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Navigation
 * @subpackage  Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Organisation\Navigation\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\Mvc\Router\Http\RouteMatch;

use Organisation\Service\OrganisationService;

/**
 * Factory for the Project admin navigation
 *
 * @package    Calendar
 * @subpackage Navigation\Service
 */
class OrganisationNavigationFactory extends DefaultNavigationFactory
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;
    /**
     * @var OrganisationService;
     */
    protected $organisationService;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param array                   $pages
     *
     * @return array
     */
    public function getExtraPages(ServiceLocatorInterface $serviceLocator, array $pages)
    {
        $application               = $serviceLocator->get('Application');
        $this->routeMatch          = $application->getMvcEvent()->getRouteMatch();
        $router                    = $application->getMvcEvent()->getRouter();
        $this->organisationService = $serviceLocator->get('organisation_organisation_service');
        $translate                 = $serviceLocator->get('viewhelpermanager')->get('translate');

        if (in_array($this->routeMatch->getMatchedRouteName(),
            array(
                'zfcadmin/organisation-manager/view',
                'zfcadmin/organisation-manager/edit',
            )
        )
        ) {

            $this->organisationService->setOrganisationId($this->routeMatch->getParam('id'));
            /**
             * Go over both arrays and check if the new entities can be added
             */
            $pages['organisation']['pages']['view'] = array(
                'label'      => (string)$this->organisationService->getOrganisation()->getOrganisation(),
                'route'      => 'zfcadmin/organisation-manager/view',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
                'active'     => true,
                'params'     => array(
                    'id' => $this->routeMatch->getParam('id')
                )
            );
        }

        if ($this->routeMatch->getMatchedRouteName() === 'zfcadmin/organisation-manager/edit') {

            $this->organisationService->setOrganisationId($this->routeMatch->getParam('id'));
            /**
             * Go over both arrays and check if the new entities can be added
             */

            $pages['organisation']['pages']['organisation']['pages']['edit'] = array(
                'label'      => sprintf($translate("txt-edit-organisation-%s"),
                    $this->organisationService->getOrganisation()->getOrganisation()),
                'route'      => 'zfcadmin/organisation-manager/edit',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
                'active'     => true,
                'params'     => array(
                    'id' => $this->routeMatch->getParam('id')
                )
            );
        }

        return $pages;
    }
}
