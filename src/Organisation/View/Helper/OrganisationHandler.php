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

use Zend\View\HelperPluginManager;
use Zend\View\Helper\AbstractHelper;
use Zend\Paginator\Paginator;
use Zend\Mvc\Router\RouteMatch;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

use Organisation\Service\OrganisationService;
use Organisation\Form\Search;
use Project\Service\ProjectService;
use Content\Entity\Handler;
use Content\Service\ArticleService;
use General\View\Helper\CountryMap;

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
     * @var ArticleService
     */
    protected $articleService;
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
     * @var CountryMap
     */
    protected $countryMap;
    /**
     * @var int
     */
    protected $limit = 5;

    /**
     * @param HelperPluginManager $helperPluginManager
     */
    public function __construct(HelperPluginManager $helperPluginManager)
    {
        $this->organisationService = $helperPluginManager->getServiceLocator()->get('organisation_organisation_service');
        $this->projectService      = $helperPluginManager->getServiceLocator()->get('project_project_service');
        $this->articleService      = $helperPluginManager->getServiceLocator()->get('content_article_service');
        $this->routeMatch          = $helperPluginManager->getServiceLocator()
            ->get('application')
            ->getMvcEvent()
            ->getRouteMatch();
        $this->countryMap          = $helperPluginManager->get('countryMap');
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render()
    {

        $translate = $this->getView()->plugin('translate');
        switch ($this->getHandler()->getHandler()) {

            case 'organisation':

                if (is_null($this->getOrganisationService()) ||
                    is_null($this->getOrganisationService()->getOrganisation())
                ) {
                    return ("The selected organisation cannot be found");
                }

                $this->getView()->headTitle()->append($translate("txt-organisation"));
                $this->getView()->headTitle()->append(
                    $this->getOrganisationService()->getOrganisation()->getOrganisation()
                );

                $organisationLink = $this->view->plugin('organisationLink');
                $this->getView()->headMeta()->setProperty('og:type',
                    $translate("txt-organisation")
                );
                $this->getView()->headMeta()->setProperty('og:title',
                    $this->getOrganisationService()->getOrganisation()->getOrganisation()
                );
                //$this->getView()->headMeta()->setProperty('og:description', $this->getOrganisationService()->getOrganisation()->getDescription());
                $this->getView()->headMeta()->setProperty('og:url', $organisationLink->__invoke(
                        $this->getOrganisationService(), 'view', 'social'
                    )
                );


                return $this->parseOrganisation($this->getOrganisationService());
                break;
            case 'organisation_list':

                $this->getView()->headTitle()->append($translate("txt-organisation-list"));
                $page = $this->routeMatch->getParam('page');

                return $this->parseOrganisationList($page);
                break;
            case 'organisation_project':

                if (is_null($this->getOrganisationService()) ||
                    is_null($this->getOrganisationService()->getOrganisation())
                ) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationProjectList($this->getOrganisationService());
                break;

            case 'organisation_metadata':

                if (is_null($this->getOrganisationService()) ||
                    is_null($this->getOrganisationService()->getOrganisation())
                ) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationMetadata($this->getOrganisationService());
                break;

            case 'organisation_article':
                return $this->parseOrganisationArticleList($this->getOrganisationService());

                break;

            case 'organisation_map':
                $countryMap = $this->countryMap;

                /**
                 * Collect the list of countries from the organisation and cluster
                 */
                $countries = array($this->getOrganisationService()->getOrganisation()->getCountry());
                foreach ($this->getOrganisationService()->getOrganisation()->getClusterMember() as $cluster) {
                    $countries[] = $cluster->getOrganisation()->getCountry();
                }

                return $countryMap($countries);
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
     * @param OrganisationService $organisationService
     *
     * @return string
     */
    public function parseOrganisationMetadata(OrganisationService $organisationService)
    {
        return $this->getView()->render('organisation/partial/entity/organisation-metadata',
            array('organisationService' => $organisationService));
    }

    /**
     * Produce a list of organisation
     *
     * @param $page
     *
     * @return string
     */
    public function parseOrganisationList($page)
    {
        $organisationQuery = $this->getOrganisationService()->findOrganisations(true);

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 15);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        $searchForm = new Search();

        return $this->getView()->render('organisation/partial/list/organisation',
            array(
                'paginator' => $paginator,
                'form'      => $searchForm
            )
        );
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
     * @param OrganisationService $organisationService
     *
     * @return \Content\Entity\Article[]
     */
    public function parseOrganisationArticleList(OrganisationService $organisationService)
    {
        $articles = $this->articleService->findArticlesByOrganisation(
            $organisationService->getOrganisation(),
            $this->getLimit()
        );

        /**
         * Parse the organisationService in to have the these functions available in the view
         */

        return $this->getView()->render('organisation/partial/list/article.twig', array(
            'organisationService' => $organisationService,
            'articles'            => $articles,
            'limit'               => $this->getLimit(),
        ));
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

        if (is_null($organisationService)) {
            return null;
        }

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

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
