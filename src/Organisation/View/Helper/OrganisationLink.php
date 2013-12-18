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
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

use Organisation\Entity;
use Organisation\Service;

/**
 * Create a link to an organisation
 *
 * @category    Organisation
 * @package     View
 * @subpackage  Helper
 */
class OrganisationLink extends AbstractHelper
{

    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * @param HelperPluginManager $helperPluginManager
     */
    public function __construct(HelperPluginManager $helperPluginManager)
    {
        $this->sm = $helperPluginManager->getServiceLocator();
    }

    /**
     * @param Service\OrganisationService $organisationService
     * @param string                      $action
     * @param string                      $show
     * @param null                        $branch
     * @param null                        $page
     * @param null                        $alternativeShow
     *
     * @return string
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(
        Service\OrganisationService $organisationService = null,
        $action = 'view',
        $show = 'name',
        $branch = null,
        $page = null,
        $alternativeShow = null)
    {

        $router  = $this->sm->get('router');
        $request = $this->sm->get('request');

        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $serverUrl = $this->view->plugin('serverUrl');
        $isAllowed = $this->view->plugin('isAllowed');

//        if (!$isAllowed('organisation', $action)) {
//            if ($action === 'view' && $show === 'name') {
//                return $organisationService;
//            }
//
//            return '';
//        }

        $routeMatch = $this->view->getHelperPluginManager()->getServiceLocator()
            ->get('application')
            ->getMvcEvent()
            ->getRouteMatch();

        $params = array(
            'entity' => 'organisation'
        );


        switch ($action) {
            case 'new':
                $router              = 'zfcadmin/organisation-manager/new';
                $text                = sprintf($translate("txt-new-organisation"));
                $organisationService = new Entity\Organisation();
                break;
            case 'edit':
                $router = 'zfcadmin/organisation-manager/edit';
                $text   = sprintf($translate("txt-edit-organisation-%s"),
                    $organisationService->parseOrganisationWithBranch($branch)
                );
                break;
            case 'list':

                $matchedRoute = $router->match($request);
                /**
                 * For a list in the front-end simply use the MatchedRouteName
                 */
                $router = $matchedRoute->getMatchedRouteName();
                /**
                 * Push the docRef in the params array
                 */
                $params['docRef'] = $matchedRoute->getParam('docRef');

                $text                = sprintf($translate("txt-list-projects"));
                $organisationService = new Service\OrganisationService();
                $organisation        = new Entity\Organisation();
                $organisationService->setOrganisation($organisation);
                break;
            case 'view':
                $router           = 'route-' . $organisationService->getOrganisation()->get("underscore_full_entity_name");
                $params['docRef'] = $organisationService->getOrganisation()->getDocRef();
                $text             = sprintf($translate("txt-view-organisation-%s"),
                    $organisationService->parseOrganisationWithBranch($branch)
                );
                break;
            case 'view-article':
                $router           = 'route-' . $organisationService->getOrganisation()->get('underscore_full_entity_name') . '-article';
                $text             = sprintf($translate("txt-view-article-for-organisation-%s"),
                    $organisationService->parseOrganisationWithBranch($branch)
                );
                $params['docRef'] = $organisationService->getOrganisation()->getDocRef();
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }

        if (is_null($organisationService)) {
            throw new \RuntimeException(
                sprintf(
                    "organisation needs to be an instance of %s, %s given in %s",
                    "Organisation\Entity\Organisation",
                    get_class($organisationService),
                    __CLASS__
                )
            );
        }


        $params['id']   = $organisationService->getOrganisation()->getId();
        $params['page'] = !is_null($page) ? $page : null;

        $classes     = array();
        $linkContent = array();

        switch ($show) {
            case 'icon':
                if ($action === 'edit') {
                    $linkContent[] = '<span class="glyphicon glyphicon-edit"></span>';
                } else {
                    $linkContent[] = '<span class="glyphicon glyphicon-info-sign"></span>';
                }
                break;
            case 'button':
                $linkContent[] = '<span class="glyphicon glyphicon-info"></span> ' . $text;
                $classes[]     = "btn btn-primary";
                break;
            case 'name':
                $linkContent[] = $organisationService->parseOrganisationWithBranch($branch);
                break;
            case 'more':
                $linkContent[] = $translate("txt-read-more");
                break;
            case 'paginator':

                $linkContent[] = $alternativeShow;
                break;
            default:
                $linkContent[] = $organisationService->parseOrganisationWithBranch($branch);
                break;
        }


        $uri = '<a href="%s" title="%s" class="%s">%s</a>';

        return sprintf(
            $uri,
            $serverUrl->__invoke() . $url($router, $params),
            $text,
            implode($classes),
            implode($linkContent)
        );
    }
}
