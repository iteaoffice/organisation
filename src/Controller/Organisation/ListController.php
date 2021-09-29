<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\Organisation;

use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\Http\Request;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Form\OrganisationFilterForm;
use Organisation\Search\Service\OrganisationSearchService;
use Organisation\Service\OrganisationService;
use Search\Form\SearchResult;
use Search\Paginator\Adapter\SolariumPaginator;
use Solarium\QueryType\Select\Query\Query as SolariumQuery;

use function http_build_query;
use function implode;
use function sprintf;

/**
 * Class ListController
 * @package Organisation\Controller\Organisation
 */
final class ListController extends AbstractController
{
    private OrganisationService $organisationService;
    private OrganisationSearchService $searchService;

    public function __construct(OrganisationService $organisationService, OrganisationSearchService $organisationSearchService)
    {
        $this->organisationService = $organisationService;
        $this->searchService       = $organisationSearchService;
    }

    public function organisationAction(): ViewModel
    {
        /** @var Request $request */
        $request      = $this->getRequest();
        $page         = $this->params('page', 1);
        $form         = new SearchResult();
        $data         = array_merge(
            [
                'order'     => '',
                'direction' => '',
                'query'     => '',
                'facet'     => [],
            ],
            $request->getQuery()->toArray()
        );

        if ($request->isGet()) {
            $this->searchService->setSearch($data['query'], [], $data['order'], $data['direction']);
            if (isset($data['facet'])) {
                foreach ($data['facet'] as $facetField => $values) {
                    $quotedValues = [];
                    foreach ($values as $value) {
                        $quotedValues[] = sprintf('"%s"', $value);
                    }

                    $this->searchService->addFilterQuery(
                        $facetField,
                        implode(' ' . SolariumQuery::QUERY_OPERATOR_OR . ' ', $quotedValues)
                    );
                }
            }

            $form->addSearchResults(
                $this->searchService->getQuery()->getFacetSet(),
                $this->searchService->getResultSet()->getFacetSet()
            );
            $form->setData($data);
        }

        $paginator = new Paginator(
            new SolariumPaginator($this->searchService->getSolrClient(), $this->searchService->getQuery())
        );
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? 1000 : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        return new ViewModel(
            [
                'form'      => $form,
                'order'     => $data['order'],
                'direction' => $data['direction'],
                'query'     => $data['query'],
                'badges'    => $form->getBadges(),
                'arguments' => http_build_query($form->getFilteredData()),
                'paginator' => $paginator,
            ]
        );
    }

    public function duplicateAction(): ViewModel
    {
        $page              = $this->params()->fromRoute('page', 1);
        $filterPlugin      = $this->getOrganisationFilter();
        $organisationQuery = $this->organisationService
            ->findDuplicateOrganisations($filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new OrganisationFilterForm($this->organisationService);
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'           => $paginator,
                'form'                => $form,
                'encodedFilter'       => urlencode($filterPlugin->getHash()),
                'organisationService' => $this->organisationService,
                'order'               => $filterPlugin->getOrder(),
                'direction'           => $filterPlugin->getDirection(),
            ]
        );
    }

    public function inactiveAction(): ViewModel
    {
        $inactiveOrganisations = $this->organisationService->findInactiveOrganisations();

        return new ViewModel(
            [
                'inactiveOrganisations' => $inactiveOrganisations
            ]
        );
    }

    public function financialAction(): ViewModel
    {
        $page              = $this->params()->fromRoute('page', 1);
        $filterPlugin      = $this->getOrganisationFilter();
        $organisationQuery = $this->organisationService->findOrganisationFinancialList($filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new OrganisationFilterForm($this->organisationService);
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'     => $paginator,
                'form'          => $form,
                'encodedFilter' => urlencode($filterPlugin->getHash()),
                'order'         => $filterPlugin->getOrder(),
                'direction'     => $filterPlugin->getDirection(),
            ]
        );
    }

    public function noFinancialAction(): ViewModel
    {
        $page              = $this->params()->fromRoute('page', 1);
        $filterPlugin      = $this->getOrganisationFilter();
        $organisationQuery = $this->organisationService
            ->findActiveOrganisationWithoutFinancial($filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new OrganisationFilterForm($this->organisationService);
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'           => $paginator,
                'form'                => $form,
                'encodedFilter'       => urlencode($filterPlugin->getHash()),
                'order'               => $filterPlugin->getOrder(),
                'direction'           => $filterPlugin->getDirection(),
                'organisationService' => $this->organisationService,
            ]
        );
    }
}
