<?php
/**
 * ITEA Office all rights reserved
 *
 * @category   General
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (http://itea3.org)
 */
declare(strict_types=1);

namespace Organisation\View\Handler;

use Content\Entity\Content;
use Content\Navigation\Service\UpdateNavigationService;
use Content\Service\ArticleService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use General\View\Helper\CountryMap;
use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;
use Organisation\View\Helper\OrganisationLink;
use Project\Options\ModuleOptions;
use Project\Service\ProjectService;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Application;
use Zend\Paginator\Paginator;
use Zend\View\HelperPluginManager;
use ZfcTwig\View\TwigRenderer;

/**
 * Class ProjectHandler
 *
 * @package Project\View\Handler
 */
final class OrganisationHandler extends AbstractHandler
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
     * @var ModuleOptions
     */
    protected $moduleOptions;
    /**
     * @var ArticleService
     */
    protected $articleService;

    /**
     * OrganisationHandler constructor.
     *
     * @param Application             $application
     * @param HelperPluginManager     $helperPluginManager
     * @param TwigRenderer            $renderer
     * @param AuthenticationService   $authenticationService
     * @param UpdateNavigationService $updateNavigationService
     * @param TranslatorInterface     $translator
     * @param OrganisationService     $organisationService
     * @param ModuleOptions           $moduleOptions
     * @param ProjectService          $projectService
     * @param ArticleService          $articleService
     */
    public function __construct(
        Application $application,
        HelperPluginManager $helperPluginManager,
        TwigRenderer $renderer,
        AuthenticationService $authenticationService,
        UpdateNavigationService $updateNavigationService,
        TranslatorInterface $translator,
        OrganisationService $organisationService,
        ModuleOptions $moduleOptions,
        ProjectService $projectService,
        ArticleService $articleService
    ) {
        parent::__construct(
            $application,
            $helperPluginManager,
            $renderer,
            $authenticationService,
            $updateNavigationService,
            $translator
        );

        $this->projectService = $projectService;
        $this->moduleOptions = $moduleOptions;
        $this->organisationService = $organisationService;
        $this->articleService = $articleService;
    }

    /**
     * @param Content $content
     *
     * @return null|string
     * @throws \Exception
     */
    public function __invoke(Content $content): ?string
    {
        $params = $this->extractContentParam($content);

        $organisation = $this->getOrganisationByParams($params);

        switch ($content->getHandler()->getHandler()) {
            case 'organisation':
                if (null === $organisation) {
                    $this->response->setStatusCode(Response::STATUS_CODE_404);

                    return 'The selected organisation cannot be found';
                }

                $this->getHeadTitle()->append($organisation->getOrganisation());

                $organisationLink = $this->helperPluginManager->get(OrganisationLink::class);
                $this->getHeadMeta()->setProperty('og:type', $this->translate("txt-organisation"));
                $this->getHeadMeta()->setProperty('og:title', $organisation->getOrganisation());
                $this->getHeadMeta()->setProperty('og:url', $organisationLink($organisation, 'view', 'social'));

                return $this->parseOrganisation($organisation);
            case 'organisation_list':
                $this->getHeadTitle()->append($this->translate("txt-organisation-list"));

                return $this->parseOrganisationList();
            default:
                return sprintf(
                    'No handler available for <code>%s</code> in class <code>%s</code>',
                    $content->getHandler()->getHandler(),
                    __CLASS__
                );
        }
    }

    /**
     * @param array $params
     *
     * @return Organisation|null
     */
    private function getOrganisationByParams(array $params): ?Organisation
    {
        $organisation = null;

        if (null !== $params['id']) {
            $organisation = $this->organisationService->findOrganisationById((int)$params['id']);
        }

        if (null !== $params['docRef']) {
            $organisation = $this->organisationService->findOrganisationByDocRef($params['docRef']);
        }

        return $organisation;
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    private function parseOrganisation(Organisation $organisation): string
    {
        return $this->renderer->render(
            'cms/organisation/organisation',
            [
                'organisation' => $organisation,
                'projectService' => $this->projectService,
                'projects'     => $this->projectService->findProjectByOrganisation(
                    $organisation,
                    ProjectService::WHICH_ONLY_ACTIVE,
                    true
                ),
                'map'          => $this->parseOrganisationMap($organisation),
                'articles'     => $this->articleService->findArticlesByOrganisation($organisation, 25),
            ]
        );
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    private function parseOrganisationMap(Organisation $organisation): string
    {
        /*
         * Collect the list of countries from the organisation and cluster
         */
        $countries = [$organisation->getCountry()];
        foreach ($organisation->getClusterMember() as $cluster) {
            $countries[] = $cluster->getOrganisation()->getCountry();
        }

        $mapOptions = [
            'clickable' => true,
            'colorMin'  => $this->moduleOptions->getCountryColorFaded(),
            'colorMax'  => $this->moduleOptions->getCountryColor(),
            'focusOn'   => ['x' => 0.5, 'y' => 0.5, 'scale' => 1.1], // Slight zoom
            'height'    => '340px',
        ];

        $countryMap = $this->helperPluginManager->get(CountryMap::class);

        return $countryMap($countries, null, $mapOptions);
    }

    /**
     * @param int $page
     *
     * @return string
     */
    private function parseOrganisationList(int $page = 1): string
    {
        $organisationQuery = $this->organisationService->findOrganisations(true, true);
        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        return $this->renderer->render(
            'cms/organisation/list',
            [
                'paginator' => $paginator,
            ]
        );
    }
}
