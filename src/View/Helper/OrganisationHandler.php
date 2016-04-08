<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\View\Helper;

use Content\Entity\Content;
use Content\Service\ArticleService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use General\View\Helper\CountryMap;
use Organisation\Entity\Organisation;
use Organisation\Options\ModuleOptions;
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;
use Zend\Mvc\Router\RouteMatch;
use Zend\Paginator\Paginator;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;
use ZfcTwig\View\TwigRenderer;

/**
 * Class OrganisationHandler.
 */
class OrganisationHandler extends AbstractHelper
{
    /**
     * @var HelperPluginManager
     */
    protected $serviceLocator;
    /**
     * @var Organisation
     */
    protected $organisation;
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
                if (is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                $this->serviceLocator->get('headtitle')->append($this->translate("txt-organisation"));
                $this->serviceLocator->get('headtitle')->append($this->getOrganisation()->getOrganisation());

                //Do now show the organisation when we don't have projects
                if (sizeof($this->getProjectService()->findProjectByOrganisation($this->getOrganisation())) === 0) {
                    $this->getServiceLocator()->get("response")->setStatusCode(404);

                    return null;
                }

                /**
                 * @var OrganisationLink $organisationLink
                 */
                $organisationLink = $this->serviceLocator->get('organisationLink');
                $this->serviceLocator->get('headmeta')->setProperty('og:type', $this->translate("txt-organisation"));
                $this->serviceLocator->get('headmeta')
                    ->setProperty('og:title', $this->getOrganisation()->getOrganisation());
                $this->serviceLocator->get('headmeta')
                    ->setProperty('og:url', $organisationLink($this->getOrganisation(), 'view', 'social'));

                return $this->parseOrganisation($this->getOrganisation());
            case 'organisation_list':
                $this->serviceLocator->get('headtitle')->append($this->translate("txt-organisation-list"));
                $page = $this->getRouteMatch()->getParam('page');

                return $this->parseOrganisationList($page);
            case 'organisation_project':
                if (is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationProjectList($this->getOrganisation());

            case 'organisation_metadata':
                if (is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationMetadata($this->getOrganisation());

            case 'organisation_article':
                if (is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationArticleList($this->getOrganisation());

            case 'organisation_title':
                if (is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationTitle($this->getOrganisation());

            case 'organisation_info':
                if (is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationInfo($this->getOrganisation());

            case 'organisation_map':
                if (is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationMap($this->getOrganisation());

            case 'organisation_logo':
                if (is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationLogo($this->getOrganisation());

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
            $this->setOrganisationByDocRef($this->getRouteMatch()->getParam('docRef'));
        }
        foreach ($content->getContentParam() as $param) {
            /*
             * When the parameterId is 0 (so we want to get the article from the URL
             */
            switch ($param->getParameter()->getParam()) {
                case 'docRef':
                    if (!is_null($docRef = $this->getRouteMatch()->getParam($param->getParameter()->getParam()))) {
                        $this->setOrganisationByDocRef($docRef);
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
                    $this->setOrganisationById($param->getParameterId());
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
    public function setOrganisationByDocRef($docRef)
    {
        $organisation = $this->getOrganisationService()->findOrganisationByDocRef($docRef);
        $this->setOrganisation($organisation);

        return $this;
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        return $this->getServiceLocator()->get(OrganisationService::class);
    }

    /**
     * @param $id
     *
     * @return OrganisationService
     */
    public function setOrganisationById($id)
    {
        $this->setOrganisation($this->getOrganisationService()->findOrganisationById($id));

        return $this;
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
     * @param Organisation $organisation
     *
     * @return null|string
     */
    public function parseOrganisation(Organisation $organisation)
    {
        return $this->getRenderer()
            ->render('organisation/partial/entity/organisation', ['organisation' => $organisation]);
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseOrganisationMap(Organisation $organisation)
    {
        /*
         * Collect the list of countries from the organisation and cluster
         */
        $countries = [$organisation->getCountry()];
        foreach ($organisation->getClusterMember() as $cluster) {
            $countries[] = $cluster->getOrganisation()->getCountry();
        }
        $options = $this->getModuleOptions();
        $mapOptions = [
            'clickable' => true,
            'colorMin'  => $options->getCountryColorFaded(),
            'colorMax'  => $options->getCountryColor(),
            'focusOn'   => ['x' => 0.5, 'y' => 0.5, 'scale' => 1.1], // Slight zoom
            'height'    => '340px',
        ];
        /**
         * @var  CountryMap $countryMap
         */
        $countryMap = $this->serviceLocator->get('countryMap');

        return $countryMap($countries, null, $mapOptions);
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseOrganisationInfo(Organisation $organisation)
    {
        return $this->getRenderer()
            ->render('organisation/partial/entity/organisation-info', ['organisation' => $organisation]);
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseOrganisationLogo(Organisation $organisation)
    {
        return $this->getRenderer()
            ->render('organisation/partial/entity/organisation-logo', ['organisation' => $organisation]);
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
        $organisationQuery = $this->getOrganisationService()->findOrganisations(true, true);
        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 15);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        return $this->getRenderer()->render('organisation/partial/list/organisation', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * @param Organisation $organisation
     *
     * @return null|string
     */
    public function parseOrganisationTitle(Organisation $organisation)
    {
        return $this->getRenderer()
            ->render('organisation/partial/entity/organisation-title', ['organisation' => $organisation]);
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseOrganisationProjectList(Organisation $organisation)
    {
        $whichProjects = $this->getProjectModuleOptions()->getProjectHasVersions() ? ProjectService::WHICH_ONLY_ACTIVE
            : ProjectService::WHICH_ALL;

        $projects = $this->getProjectService()->findProjectByOrganisation($organisation, $whichProjects, true);

        return $this->getRenderer()->render(
            'organisation/partial/list/project',
            ['projects' => $projects, 'projectService' => $this->getProjectService()]
        );
    }

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        return $this->getServiceLocator()->get(ModuleOptions::class);
    }

    /**
     * @return ProjectService
     */
    public function getProjectService()
    {
        return $this->getServiceLocator()->get(ProjectService::class);
    }

    /**
     * @return \Project\Options\ModuleOptions
     */
    public function getProjectModuleOptions()
    {
        return $this->getServiceLocator()->get(\Project\Options\ModuleOptions::class);
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseOrganisationMetadata(Organisation $organisation)
    {
        return $this->getRenderer()
            ->render('organisation/partial/entity/organisation-metadata', ['organisation' => $organisation]);
    }

    /**
     * @param Organisation $organisation
     *
     * @return \Content\Entity\Article[]
     */
    public function parseOrganisationArticleList(Organisation $organisation)
    {
        $articles = $this->getArticleService()->findArticlesByOrganisation($organisation, $this->getLimit());

        /*
         * Parse the organisationService in to have the these functions available in the view
         */

        return $this->getRenderer()->render('organisation/partial/list/article', [
            'articles'     => $articles,
            'organisation' => $this->getOrganisation(),
            'limit'        => $this->getLimit(),
        ]);
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

    /**
     * @return Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param Organisation $organisation
     *
     * @return OrganisationHandler
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }
}
