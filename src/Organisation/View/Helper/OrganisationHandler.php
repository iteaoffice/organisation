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
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Mvc\Router\Http\RouteMatch;

use Organisation\Service\OrganisationService;

use Project\Service\ProjectService;

use Content\Entity\Handler;

/**
 * Class OrganisationHandler
 * @package Organisation\View\Helper
 */
class OrganisationHandler extends AbstractHelper
{
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var Handler
     */
    protected $handler;
    /**
     * @var int
     */
    protected $year;
    /**
     * @var string
     */
    protected $docRef;
    /**
     * @var RouteMatch
     */
    protected $routeMatch = null;

    /**
     * @param HelperPluginManager $helperPluginManager
     */
    public function __construct(HelperPluginManager $helperPluginManager)
    {
        $this->organisationService = $helperPluginManager->getServiceLocator()->get('organisation_organisation_service');
        $this->projectService      = $helperPluginManager->getServiceLocator()->get('project_project_service');
        $this->routeMatch          = $helperPluginManager->getServiceLocator()
            ->get('application')
            ->getMvcEvent()
            ->getRouteMatch();
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render()
    {
        if (is_null($this->getOrganisationService()) || is_null($this->getOrganisationService()->getOrganisation())) {
//            return ("The selected organisation cannot be found");
        }

        $this->getView()->headTitle()->append("Organisation");

        switch ($this->getHandler()->getHandler()) {

            case 'organisation':

                $this->getView()->headTitle()->append($this->getOrganisationService()->getOrganisation()->getOrganisation());

                return $this->parseOrganisation($this->getOrganisationService());
                break;
            case 'organisation_list':

                $this->getView()->headTitle()->append('List');
                $page = $this->routeMatch->getParam('page');

                return $this->parseOrganisationList($page);
                break;
            case 'organisation_project':
                return $this->parseOrganisationProjectList($this->getOrganisationService());
                break;

            default:
                return sprintf("No handler available for <code>%s</code> in class <code>%s</code>",
                    $this->getHandler()->getHandler(),
                    __CLASS__);
        }
    }

    /**
     * @param OrganisationService $organisationService
     *
     * @return string
     */
    public function parseOrganisation(OrganisationService $organisationService)
    {
        return $this->getView()->render('organisation/partial/entity/organisation',
            array('organisationService' => $organisationService));
    }

    /**
     * @param $page
     *
     * @return string
     */
    public function parseOrganisationList($page)
    {
        $organisation = $this->getOrganisationService()->findAllOrganisationByYear($this->getYear());

        $paginator = new Paginator(new ArrayAdapter($organisation));
        $paginator->setDefaultItemCountPerPage(9);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        return $this->getView()->render('organisation/partial/list/organisation',
            array('paginator' => $paginator));
    }

    /**
     * @param OrganisationService $organisationService
     *
     * @return string
     */
    public function parseOrganisationProjectList(OrganisationService $organisationService)
    {

        $projects = $this->projectService->findProjectByOrganisation($organisationService->getOrganisation());

        return $this->getView()->render('organisation/partial/list/project.twig', array('projects' => $projects));
    }

    /**
     * @param \Content\Entity\Handler $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return \Content\Entity\Handler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param $projectId
     *
     * @return OrganisationService
     */
    public function setId($projectId)
    {
        $this->setOrganisationService($this->organisationService->setOrganisationId($projectId));

        return $this->getOrganisationService();
    }

    /**
     * @param $docRef
     *
     * @return OrganisationService
     */
    public function setDocRef($docRef)
    {
        $organisationService = $this->getOrganisationService()->findOrganisationByDocRef($docRef);
        $this->setOrganisationService($organisationService);

        return $this->getOrganisationService();
    }

    /**
     * @param \Organisation\Service\OrganisationService $organisationService
     */
    public function setOrganisationService($organisationService)
    {
        $this->organisationService = $organisationService;
    }

    /**
     * @return \Organisation\Service\OrganisationService
     */
    public function getOrganisationService()
    {
        return $this->organisationService;
    }
}
