<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Content\Entity\Content;
use Content\Entity\Param;
use Content\Service\ArticleService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use General\View\Helper\CountryMap;
use Organisation\Entity\Organisation;
use Organisation\Options\ModuleOptions;
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;
use Zend\Paginator\Paginator;

/**
 * Class OrganisationHandler.
 */
class OrganisationHandler extends AbstractViewHelper
{
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
     * @return string|null
     *
     * @throws \Exception
     */
    public function __invoke(Content $content)
    {
        $this->extractContentParam($content);

        switch ($content->getHandler()->getHandler()) {
            case 'organisation':
                if (\is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                $this->getHelperPluginManager()->get('headtitle')->append($this->translate("txt-organisation"));
                $this->getHelperPluginManager()->get('headtitle')->append($this->getOrganisation()->getOrganisation());

                //Do now show the organisation when we don't have projects
                if (\count($this->getProjectService()->findProjectByOrganisation($this->getOrganisation())) === 0) {
                    $this->getServiceManager()->get('response')->setStatusCode(404);

                    return null;
                }

                /**
                 * @var OrganisationLink $organisationLink
                 */
                $organisationLink = $this->getHelperPluginManager()->get('organisationLink');
                $this->getHelperPluginManager()->get('headmeta')
                    ->setProperty('og:type', $this->translate("txt-organisation"));
                $this->getHelperPluginManager()->get('headmeta')
                    ->setProperty('og:title', $this->getOrganisation()->getOrganisation());
                $this->getHelperPluginManager()->get('headmeta')
                    ->setProperty('og:url', $organisationLink($this->getOrganisation(), 'view', 'social'));

                return $this->parseOrganisation($this->getOrganisation());
            case 'organisation_list':
                $this->getHelperPluginManager()->get('headtitle')->append($this->translate("txt-organisation-list"));
                $page = $this->getRouteMatch()->getParam('page');

                return $this->parseOrganisationList($page);
            case 'organisation_project':
                if (\is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationProjectList($this->getOrganisation());

            case 'organisation_metadata':
                if (\is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationMetadata($this->getOrganisation());

            case 'organisation_article':
                if (\is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationArticleList($this->getOrganisation());

            case 'organisation_title':
                if (\is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationTitle($this->getOrganisation());

            case 'organisation_info':
                if (\is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationInfo($this->getOrganisation());

            case 'organisation_map':
                if (\is_null($this->getOrganisation())) {
                    return ("The selected organisation cannot be found");
                }

                return $this->parseOrganisationMap($this->getOrganisation());

            case 'organisation_logo':
                if (\is_null($this->getOrganisation())) {
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
        /**
         * Go over the handler params and try to see if it is hardcoded or just set via the route
         */
        foreach ($content->getHandler()->getParam() as $parameter) {
            switch ($parameter->getParam()) {
                case 'docRef':
                    $docRef = $this->findParamValueFromContent($content, $parameter);

                    if (!\is_null($docRef)) {
                        $this->setOrganisationByDocRef($docRef);
                    }
                    break;
            }
        }
    }

    /**
     * @param Content $content
     * @param Param $param
     *
     * @return null|string
     */
    private function findParamValueFromContent(Content $content, Param $param)
    {
        //Hardcoded is always first,If it cannot be found, try to find it from the docref (rule 2)
        foreach ($content->getContentParam() as $contentParam) {
            if ($contentParam->getParameter() === $param && !empty($contentParam->getParameterId())) {
                return $contentParam->getParameterId();
            }
        }

        //Try first to see if the param can be found from the route (rule 1)
        if (!\is_null($this->getRouteMatch()->getParam($param->getParam()))) {
            return $this->getRouteMatch()->getParam($param->getParam());
        }

        //If not found, take rule 3
        return null;
    }

    /**
     * @param $docRef
     *
     * @return void
     */
    private function setOrganisationByDocRef($docRef)
    {
        $organisation = $this->getOrganisationService()->findOrganisationByDocRef($docRef);

        if (\is_null($organisation)) {
            $this->getOrganisationService()->findOrganisationById((int)$docRef);
        }
        $this->setOrganisation($organisation);
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        return $this->getServiceManager()->get(OrganisationService::class);
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

    /**
     * @return ProjectService
     */
    public function getProjectService(): ProjectService
    {
        return $this->getServiceManager()->get(ProjectService::class);
    }

    /**
     * @param Organisation $organisation
     *
     * @return null|string
     */
    public function parseOrganisation(Organisation $organisation): string
    {
        return $this->getRenderer()
            ->render('organisation/partial/entity/organisation', ['organisation' => $organisation]);
    }

    /**
     * Produce a list of organisation.
     *
     * @param $page
     *
     * @return string
     */
    public function parseOrganisationList($page): string
    {
        $organisationQuery = $this->getOrganisationService()->findOrganisations(true, true);
        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        return $this->getRenderer()->render(
            'organisation/partial/list/organisation',
            [
                'paginator' => $paginator,
            ]
        );
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseOrganisationProjectList(Organisation $organisation): string
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
     * @return \Project\Options\ModuleOptions
     */
    public function getProjectModuleOptions(): \Project\Options\ModuleOptions
    {
        return $this->getServiceManager()->get(\Project\Options\ModuleOptions::class);
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseOrganisationMetadata(Organisation $organisation): string
    {
        return $this->getRenderer()
            ->render('organisation/partial/entity/organisation-metadata', ['organisation' => $organisation]);
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseOrganisationArticleList(Organisation $organisation): string
    {
        $articles = $this->getArticleService()->findArticlesByOrganisation($organisation, $this->getLimit());

        /*
         * Parse the organisationService in to have the these functions available in the view
         */

        return $this->getRenderer()->render(
            'organisation/partial/list/article',
            [
                'articles'     => $articles,
                'organisation' => $this->getOrganisation(),
                'limit'        => $this->getLimit(),
            ]
        );
    }

    /**
     * @return ArticleService
     */
    public function getArticleService(): ArticleService
    {
        return $this->getServiceManager()->get(ArticleService::class);
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
     * @param Organisation $organisation
     *
     * @return null|string
     */
    public function parseOrganisationTitle(Organisation $organisation): string
    {
        return $this->getRenderer()
            ->render('organisation/partial/entity/organisation-title', ['organisation' => $organisation]);
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseOrganisationInfo(Organisation $organisation): string
    {
        return $this->getRenderer()
            ->render('organisation/partial/entity/organisation-info', ['organisation' => $organisation]);
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseOrganisationMap(Organisation $organisation): string
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
        $countryMap = $this->getHelperPluginManager()->get('countryMap');

        return $countryMap($countries, null, $mapOptions);
    }

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions(): ModuleOptions
    {
        return $this->getServiceManager()->get(ModuleOptions::class);
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseOrganisationLogo(Organisation $organisation): string
    {
        return $this->getRenderer()
            ->render('organisation/partial/entity/organisation-logo', ['organisation' => $organisation]);
    }
}
