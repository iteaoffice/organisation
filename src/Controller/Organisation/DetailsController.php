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

use Affiliation\Service\AffiliationService;
use Affiliation\Service\DoaService;
use Affiliation\Service\LoiService;
use Doctrine\ORM\EntityManager;
use Invoice\Search\Service\InvoiceSearchService;
use Laminas\Http\Request;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Form\Organisation\MergeForm;
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;
use Search\Form\SearchResult;
use Search\Paginator\Adapter\SolariumPaginator;
use Solarium\QueryType\Select\Query\Query as SolariumQuery;

use function http_build_query;
use function implode;

/**
 * Class DetailsController
 * @package Organisation\Controller\Organisation
 */
final class DetailsController extends AbstractController
{
    private OrganisationService $organisationService;
    private InvoiceSearchService $invoiceSearchService;
    private ProjectService $projectService;
    private AffiliationService $affiliationService;
    private DoaService $doaService;
    private LoiService $loiService;
    private EntityManager $entityManager;

    public function __construct(
        OrganisationService $organisationService,
        InvoiceSearchService $invoiceSearchService,
        ProjectService $projectService,
        AffiliationService $affiliationService,
        DoaService $doaService,
        LoiService $loiService,
        EntityManager $entityManager
    ) {
        $this->organisationService  = $organisationService;
        $this->invoiceSearchService = $invoiceSearchService;
        $this->projectService       = $projectService;
        $this->affiliationService   = $affiliationService;
        $this->doaService           = $doaService;
        $this->loiService           = $loiService;
        $this->entityManager        = $entityManager;
    }

    public function generalAction(): ViewModel
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'organisation'        => $organisation,
            'organisationService' => $this->organisationService,
            'tab'                 => 'general'
        ]);
    }

    public function parentAction(): ViewModel
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'organisation' => $organisation,
            'tab'          => 'parent'
        ]);
    }

    public function financialAction(): ViewModel
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'organisation'        => $organisation,
            'organisationService' => $this->organisationService,
            'tab'                 => 'financial'
        ]);
    }

    public function invoicesAction(): ViewModel
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        /** @var Request $request */
        $request = $this->getRequest();
        $page    = $this->params('page', 1);
        $form    = new SearchResult();
        $data    = array_merge(
            [
                'order'     => '',
                'direction' => '',
                'query'     => '',
                'facet'     => [],
            ],
            $request->getQuery()->toArray()
        );

        $searchFields = [
            'invoice_number_search',
            'organisation_search',
            'contact_search',
            'reference_search',
            'type_search',
            'vat_type_search',
            'status_name_search',
            'status_explanation_search',
            'day_book_number_search',
        ];

        if ($request->isGet()) {
            $this->invoiceSearchService->setSearchByOrganisation(
                $organisation,
                $data['query'],
                $searchFields,
                $data['order'],
                $data['direction']
            );
            if (isset($data['facet'])) {
                foreach ($data['facet'] as $facetField => $values) {
                    $quotedValues = [];
                    foreach ($values as $value) {
                        $quotedValues[] = (string)$value;
                    }

                    $this->invoiceSearchService->addFilterQuery(
                        $facetField,
                        implode(' ' . SolariumQuery::QUERY_OPERATOR_OR . ' ', $quotedValues)
                    );
                }
            }

            $form->addSearchResults(
                $this->invoiceSearchService->getQuery()->getFacetSet(),
                $this->invoiceSearchService->getResultSet()->getFacetSet()
            );
            $form->setData($data);
        }

        $paginator = new Paginator(
            new SolariumPaginator($this->invoiceSearchService->getSolrClient(), $this->invoiceSearchService->getQuery())
        );
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? 1000 : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        return new ViewModel([
            'organisation' => $organisation,
            'form'         => $form,
            'order'        => $data['order'],
            'direction'    => $data['direction'],
            'query'        => $data['query'],
            'badges'       => $form->getBadges(),
            'arguments'    => http_build_query($form->getFilteredData()),
            'paginator'    => $paginator,
            'tab'          => 'invoices'
        ]);
    }

    public function legalAction(): ViewModel
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'organisation'   => $organisation,
            'affiliationDoa' => $this->doaService->findDoaByOrganisation($organisation),
            'affiliationLoi' => $this->loiService->findLoiByOrganisation($organisation),
            'tab'            => 'legal'
        ]);
    }

    public function notesAction(): ViewModel
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'organisation' => $organisation,
            'tab'          => 'notes'
        ]);
    }

    public function contactsAction(): ViewModel
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'organisation'   => $organisation,
            'projectService' => $this->projectService,
            'tab'            => 'contacts'
        ]);
    }

    public function projectsAction(): ViewModel
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'organisation'   => $organisation,
            'projectService' => $this->projectService,
            'affiliations'   => $this->affiliationService->findAffiliationByOrganisation($organisation),
            'tab'            => 'projects'
        ]);
    }

    public function ideasAction(): ViewModel
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'organisation' => $organisation,
            'tab'          => 'ideas'
        ]);
    }

    public function mergeAction(): ViewModel
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        $form = new MergeForm($this->entityManager, $organisation);

        return new ViewModel(
            [

                'organisation'        => $organisation,
                'organisationService' => $this->organisationService,
                'form'                => $form,
                'tab'                 => 'merge'
            ]
        );
    }
}
