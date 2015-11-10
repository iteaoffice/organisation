<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\Navigation\Service;

use Organisation\Service\OrganisationService;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the Project admin navigation.
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
     * @param array $pages
     *
     * @return array
     */
    public function getExtraPages(ServiceLocatorInterface $serviceLocator, array $pages)
    {
        $application = $serviceLocator->get('Application');
        $this->routeMatch = $application->getMvcEvent()->getRouteMatch();
        $router = $application->getMvcEvent()->getRouter();
        $this->organisationService = $serviceLocator->get(OrganisationService::class);
        $translate = $serviceLocator->get('viewhelpermanager')->get('translate');
        if (in_array(
            $this->routeMatch->getMatchedRouteName(),
            [
                'zfcadmin/organisation/view',
                'zfcadmin/organisation/edit',
            ]
        )
        ) {
            $this->organisationService->setOrganisationId($this->routeMatch->getParam('id'));
            /*
             * Go over both arrays and check if the new entities can be added
             */
            $pages['organisation']['pages']['view'] = [
                'label'      => (string)$this->organisationService->getOrganisation()->getOrganisation(),
                'route'      => 'zfcadmin/organisation/view',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
                'active'     => true,
                'params'     => [
                    'id' => $this->routeMatch->getParam('id'),
                ],
            ];
        }
        if ($this->routeMatch->getMatchedRouteName() === 'zfcadmin/organisation/edit') {
            $this->organisationService->setOrganisationId($this->routeMatch->getParam('id'));
            /*
             * Go over both arrays and check if the new entities can be added
             */
            $pages['organisation']['pages']['organisation']['pages']['edit'] = [
                'label'      => sprintf(
                    $translate("txt-edit-organisation-%s"),
                    $this->organisationService->getOrganisation()->getOrganisation()
                ),
                'route'      => 'zfcadmin/organisation/edit',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
                'active'     => true,
                'params'     => [
                    'id' => $this->routeMatch->getParam('id'),
                ],
            ];
        }

        return $pages;
    }
}
