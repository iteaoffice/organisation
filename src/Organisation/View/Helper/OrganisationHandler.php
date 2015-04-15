<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Organisation\View\Helper;

use Content\Entity\Content;
use Content\Service\ArticleService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use General\View\Helper\CountryMap;
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Mvc\Router\RouteMatch;
use Zend\Paginator\Paginator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;
use ZfcTwig\View\TwigRenderer;

/**
 * Class OrganisationHandler.
 */
class OrganisationHandler extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var HelperPluginManager
     */
    protected $serviceLocator;
    /**
     * @var int
     */
    protected $limit = 5;

    /**
     * @param Content $content
     *
     * @return string|void
     *
     * @throws \Exception
     */
    public function __invoke(Content $content)
    {
        $this->extractContentParam($content);

        switch ($content->getHandler()->getHandler()) {
            case 'organisation':
                if ($this->getOrganisationService()->isEmpty()) {
                    return ("The selected organisation cannot be found");
                }

                $this->serviceLocator->get('headtitle')->append($this->translate("txt-organisation"));
                $this->serviceLocator->get('headtitle')->append(
                    $this->getOrganisationService()->getOrganisation()->getOrganisation()
                );

                //Do now show the organisation when we don't have projects
                if (
                    sizeof(
                        $this->getProjectService()->findProjectByOrganisation(
                            $this->getOrganisationService()->getOrganisation()
                        )
                    ) === 0
                ) {
                    $this->getServiceLocator()->get("response")->setStatusCode(404);

                    return;
                }

                /*
                 * @var OrganisationLink
                 */
                $organisationLink = $this->serviceLocator->get('organisationLink');
                $this->serviceLocator->get('headmeta')->setProperty(
                    'og:type',
                    $this->translate("txt-organisation")
                );
                $this->serviceLocator->get('headmeta')->setProperty(
                    'og:title',
                    $this->getOrganisationService()->getOrganisation()->getOrganisation()
                );
                $this->serviceLocator->get('headmeta')->setProperty(
                    'og:url',
                    $organisationLink->__invoke(
                        $this->getOrganisationService(),
                        'view',
                        'social'
                    )
                );

                return $this->parseOrganisation($this->getOrganisationService());
            case 'organisation_list':
                $this->serviceLocator->get('headtitle')->append($this->translate("txt-organisation-list"));
                $page = $this->getRouteMatch()->getParam('page');

                return $this->parseOrganisationList($page);
            case 'organisation_project':
                if ($this->getOrganisationService()->isEmpty()) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationProjectList($this->getOrganisationService());
                break;
            case 'organisation_metadata':
                if ($this->getOrganisationService()->isEmpty()) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationMetadata($this->getOrganisationService());
                break;
            case 'organisation_article':
                if ($this->getOrganisationService()->isEmpty()) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationArticleList($this->getOrganisationService());
                break;

            case 'organisation_title':
                if ($this->getOrganisationService()->isEmpty()) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationTitle($this->getOrganisationService());
                break;

            case 'organisation_info':
                if ($this->getOrganisationService()->isEmpty()) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationInfo($this->getOrganisationService());
                break;

            case 'organisation_map':
                if ($this->getOrganisationService()->isEmpty()) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationMap($this->getOrganisationService());
                break;

            default:
                return sprintf(
                    "No handler available for <code>%s</code> in class <code>%s</code>",
                    $content->getHandler()->getHandler(),
                    __CLASS__
                );
        }
    }

    /**
     * @param Content $content
     */
    public function extractContentParam(Content $content)
    {
        //Give default the docRef to the handler, this does not harm
        if (!is_null($this->getRouteMatch()->getParam('docRef'))) {
            $this->setDocRef($this->getRouteMatch()->getParam('docRef'));
        }
        foreach ($content->getContentParam() as $param) {
            /*
             * When the parameterId is 0 (so we want to get the article from the URL
             */
            switch ($param->getParameter()->getParam()) {
                case 'docRef':
                    if (!is_null($docRef = $this->getRouteMatch()->getParam($param->getParameter()->getParam()))) {
                        $this->setDocRef($docRef);
                    }
                    break;
                case 'limit':
                    if ('0' === $param->getParameterId()) {
                        $limit = null;
                    } else {
                        $limit = $param->getParameterId();
                    }
                    $this->setLimit($limit);
                    break;
                case 'organisation':
                    $this->setId($param->getParameterId());
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @return RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->getServiceLocator()->get('application')->getMvcEvent()->getRouteMatch();
    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator->getServiceLocator();
    }

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AbstractHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * @param $docRef
     *
     * @return OrganisationService
     */
    public function setDocRef($docRef)
    {
        return $this->getOrganisationService()->findOrganisationByDocRef($docRef);
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        return $this->getServiceLocator()->get(OrganisationService::class);
    }

    /**
     * @param $projectId
     *
     * @return OrganisationService
     */
    public function setId($projectId)
    {
        return $this->getOrganisationService()->setOrganisationId($projectId);
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function translate($string)
    {
        return $this->serviceLocator->get('translate')->__invoke($string);
    }

    /**
     * @param OrganisationService $organisationService
     *
     * @return string
     */
    public function parseOrganisation(OrganisationService $organisationService)
    {
        return $this->getRenderer()->render(
            'organisation/partial/entity/organisation',
            ['organisationService' => $organisationService]
        );
    }

    /**
     * @param OrganisationService $organisationService
     *
     * @return string
     */
    public function parseOrganisationMap(OrganisationService $organisationService)
    {
        /*
         * Collect the list of countries from the organisation and cluster
         */
        $countries = [$this->getOrganisationService()->getOrganisation()->getCountry()];
        foreach ($this->getOrganisationService()->getOrganisation()->getClusterMember() as $cluster) {
            $countries[] = $cluster->getOrganisation()->getCountry();
        }

        if (DEBRANOVA_HOST == 'artemisia') {
            return $this->getRenderer()->render(
                'organisation/partial/entity/organisation-map',
                ['countries' => $countries]
            );
        }
        /*
         * @var CountryMap
         */
        $countryMap = $this->serviceLocator->get('countryMap');

        return $countryMap->__invoke($countries);
    }

    /**
     * @param OrganisationService $organisationService
     *
     * @return string
     */
    public function parseOrganisationInfo(OrganisationService $organisationService)
    {
        return $this->getRenderer()->render(
            'organisation/partial/entity/organisation-info',
            ['organisationService' => $organisationService]
        );
    }

    /**
     * @return TwigRenderer
     */
    public function getRenderer()
    {
        return $this->getServiceLocator()->get('ZfcTwigRenderer');
    }

    /**
     * Produce a list of organisation.
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

        return $this->getRenderer()->render(
            'organisation/partial/list/organisation',
            [
                'paginator' => $paginator,
            ]
        );
    }

    public function parseOrganisationTitle()
    {
        return $this->getRenderer()->render(
            'organisation/partial/entity/organisation-title',
            ['organisationService' => $this->getOrganisationService()]
        );
    }

    /**
     * @param OrganisationService $organisationService
     *
     * @return string
     */
    public function parseOrganisationProjectList(OrganisationService $organisationService)
    {
        $success = false;
        $config = $this->getConfig();

        $key = $config['cache_key'].'-organisation-project-list-html-organisation-'.
            $organisationService->getOrganisation()->getId();
        $html = $this->getCache()->getItem($key, $success);

        if (true || !$success) {
            $whichProjects = $this->getProjectService()->getOptions()->getProjectHasVersions(
            ) ? ProjectService::WHICH_ONLY_ACTIVE : ProjectService::WHICH_ALL;

            $whichTemplate = $this->getProjectService()->getOptions()->getProjectHasVersions(
            ) ? 'organisation/partial/list/project' : 'organisation/partial/list/project_eu';

            $projects = $this->getProjectService()->findProjectByOrganisation(
                $organisationService->getOrganisation(),
                $whichProjects,
                true
            );

            $html = $this->getRenderer()->render(
                $whichTemplate,
                ['projects' => $projects]
            );
            $this->getCache()->setItem($key, $html);
        }

        return $html;
    }

    /**
     * @return []
     */
    public function getConfig()
    {
        return $this->getServiceLocator()->get('organisation_module_config');
    }

    /**
     * @return AbstractAdapter
     */
    public function getCache()
    {
        return $this->getServiceLocator()->get('organisation_cache');
    }

    /**
     * @return ProjectService
     */
    public function getProjectService()
    {
        return $this->getServiceLocator()->get(ProjectService::class);
    }

    /**
     * @param OrganisationService $organisationService
     *
     * @return string
     */
    public function parseOrganisationMetadata(OrganisationService $organisationService)
    {
        return $this->getRenderer()->render(
            'organisation/partial/entity/organisation-metadata',
            ['organisationService' => $organisationService]
        );
    }

    /**
     * @param OrganisationService $organisationService
     *
     * @return \Content\Entity\Article[]
     */
    public function parseOrganisationArticleList(OrganisationService $organisationService)
    {
        $articles = $this->getArticleService()->findArticlesByOrganisation(
            $organisationService->getOrganisation(),
            $this->getLimit()
        );

        /*
         * Parse the organisationService in to have the these functions available in the view
         */
        return $this->getRenderer()->render(
            'organisation/partial/list/article',
            [
                'organisationService' => $organisationService,
                'articles'            => $articles,
                'limit'               => $this->getLimit(),
            ]
        );
    }

    /**
     * @return ArticleService
     */
    public function getArticleService()
    {
        return $this->getServiceLocator()->get(ArticleService::class);
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
}
