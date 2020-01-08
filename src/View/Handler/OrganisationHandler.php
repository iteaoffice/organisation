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
use Content\Service\ArticleService;
use General\ValueObject\Link\LinkDecoration;
use General\View\Handler\AbstractHandler;
use General\View\Helper\Country\CountryMap;
use Organisation\Entity\Organisation;
use Organisation\Search\Service\OrganisationSearchService;
use Organisation\Service\OrganisationService;
use Organisation\View\Helper\OrganisationLink;
use Project\Service\ProjectService;
use Search\Form\SearchResult;
use Search\Paginator\Adapter\SolariumPaginator;
use Solarium\QueryType\Select\Query\Query as SolariumQuery;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Application;
use Laminas\Paginator\Paginator;
use Laminas\View\HelperPluginManager;
use ZfcTwig\View\TwigRenderer;

use function http_build_query;
use function in_array;
use function sprintf;

/**
 * Class OrganisationHandler
 * @package Organisation\View\Handler
 */
final class OrganisationHandler extends AbstractHandler
{
    private OrganisationService $organisationService;
    private OrganisationSearchService $organisationSearchService;
    private ProjectService $projectService;
    private ArticleService $articleService;

    public function __construct(
        Application $application,
        HelperPluginManager $helperPluginManager,
        TwigRenderer $renderer,
        AuthenticationService $authenticationService,
        TranslatorInterface $translator,
        OrganisationService $organisationService,
        OrganisationSearchService $organisationSearchService,
        ProjectService $projectService,
        ArticleService $articleService
    ) {
        parent::__construct(
            $application,
            $helperPluginManager,
            $renderer,
            $authenticationService,
            $translator
        );

        $this->projectService = $projectService;
        $this->organisationService = $organisationService;
        $this->organisationSearchService = $organisationSearchService;
        $this->articleService = $articleService;
    }

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
                $this->getHeadMeta()->setProperty('og:type', $this->translator->translate('txt-organisation'));
                $this->getHeadMeta()->setProperty('og:title', $organisation->getOrganisation());
                $this->getHeadMeta()->setProperty('og:url', $organisationLink($organisation, 'view', LinkDecoration::SHOW_RAW));

                return $this->parseOrganisation($organisation);
            case 'organisation_list':
                $this->getHeadTitle()->append($this->translator->translate('txt-organisation-list'));

                return $this->parseOrganisationList();
            default:
                return sprintf(
                    'No handler available for <code>%s</code> in class <code>%s</code>',
                    $content->getHandler()->getHandler(),
                    __CLASS__
                );
        }
    }

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

    private function parseOrganisation(Organisation $organisation): string
    {
        return $this->renderer->render(
            'cms/organisation/organisation',
            [
                'organisation' => $organisation,
                'projectService' => $this->projectService,
                'projects' => $this->projectService->findProjectByOrganisation(
                    $organisation
                ),
                'map' => $this->parseOrganisationMap($organisation)
            ]
        );
    }

    private function parseOrganisationMap(Organisation $organisation): string
    {
        /*
         * Collect the list of countries from the organisation and cluster
         */
        $countries = [$organisation->getCountry()];
        $mapOptions = [
            'clickable' => true,
            'colorMin' => '#00a651',
            'colorMax' => '#005C00',
            'focusOn' => ['x' => 0.5, 'y' => 0.5, 'scale' => 1.1], // Slight zoom
            'height' => '340px',
        ];

        $countryMap = $this->helperPluginManager->get(CountryMap::class);

        return $countryMap($countries, null, $mapOptions);
    }

    private function parseOrganisationList(): string
    {
        $page = $this->routeMatch->getParam('page', 1);

        $form = new SearchResult();
        $data = array_merge(
            [
                'order' => '',
                'direction' => '',
                'query' => '',
                'facet' => [],
            ],
            $this->request->getQuery()->toArray()
        );
        $searchFields = ['organisation_search', 'country_search', 'organisation_type_search'];
        $hasTerm = ! in_array($data['query'], ['*', ''], true);

        if ($this->request->isGet()) {
            $this->organisationSearchService->setSearchForWebsite(
                $data['query'],
                $searchFields,
                $data['order'],
                $data['direction']
            );
            if (isset($data['facet'])) {
                foreach ($data['facet'] as $facetField => $values) {
                    $quotedValues = [];

                    foreach ($values as $value) {
                        $quotedValues[] = sprintf('"%s"', $value);
                    }

                    $this->organisationSearchService->addFilterQuery(
                        $facetField,
                        implode(' ' . SolariumQuery::QUERY_OPERATOR_OR . ' ', $quotedValues)
                    );
                }
            }

            $form->addSearchResults(
                $this->organisationSearchService->getQuery()->getFacetSet(),
                $this->organisationSearchService->getResultSet()->getFacetSet()
            );
            $form->setData($data);
        }

        $paginator = new Paginator(
            new SolariumPaginator(
                $this->organisationSearchService->getSolrClient(),
                $this->organisationSearchService->getQuery()
            )
        );
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? 1000 : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));


        return $this->renderer->render(
            'cms/organisation/list',
            [
                'form' => $form,
                'order' => $data['order'],
                'direction' => $data['direction'],
                'query' => $data['query'],
                'badges' => $form->getBadges(),
                'arguments' => http_build_query($form->getFilteredData()),
                'paginator' => $paginator,
                'page' => $page,
                'hasTerm' => $hasTerm,
                'organisationService' => $this->organisationService,
                'route' => $this->routeMatch->getMatchedRouteName(),
                'params' => $this->routeMatch->getParams(),
                'docRef' => $this->routeMatch->getParam('docRef')
            ]
        );
    }
}
