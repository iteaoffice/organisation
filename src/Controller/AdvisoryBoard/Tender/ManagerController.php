<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\AdvisoryBoard\Tender;

use Laminas\Http\Request;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Entity;
use Organisation\Search\Service\AdvisoryBoard\TenderSearchService;
use Organisation\Service\AdvisoryBoard\TenderService;
use Organisation\Service\FormService;
use Search\Form\SearchResult;
use Search\Paginator\Adapter\SolariumPaginator;
use Solarium\QueryType\Select\Query\Query as SolariumQuery;

final class ManagerController extends AbstractController
{
    private TenderService $tenderService;
    private TenderSearchService $searchService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(TenderService $tenderService, TenderSearchService $tenderSearchService, FormService $formService, TranslatorInterface $translator)
    {
        $this->tenderService = $tenderService;
        $this->searchService = $tenderSearchService;
        $this->formService   = $formService;
        $this->translator    = $translator;
    }


    public function listAction(): ViewModel
    {
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

    public function newAction()
    {
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare(new Entity\AdvisoryBoard\Tender(), $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/advisory-board/tender/list');
            }

            if ($form->isValid()) {
                /* @var $tender Entity\AdvisoryBoard\Tender */
                $tender = $form->getData();

                $tender = $this->tenderService->save($tender);

                $this->flashMessenger()->addSuccessMessage($this->translator->translate("txt-tender-has-been-created-successfully"));

                return $this->redirect()->toRoute(
                    'zfcadmin/advisory-board/tender/details/general',
                    [
                        'id' => $tender->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction()
    {
        $tender = $this->tenderService->findTenderById((int)$this->params('id'));

        if (null === $tender) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($tender, $data);

        if (! $this->tenderService->canDeleteTender($tender)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/advisory-board/tender/details/general',
                    [
                        'id' => $tender->getId(),
                    ]
                );
            }

            if (isset($data['delete']) && $this->tenderService->canDeleteTender($tender)) {
                $this->tenderService->delete($tender);

                $this->flashMessenger()->addSuccessMessage($this->translator->translate("txt-tender-has-been-deleted-successfully"));

                return $this->redirect()->toRoute('zfcadmin/advisory-board/tender/list');
            }

            if ($form->isValid()) {
                /** @var Entity\AdvisoryBoard\Tender $tender */
                $tender = $form->getData();

                $this->tenderService->save($tender);

                $this->flashMessenger()->addSuccessMessage($this->translator->translate("txt-tender-has-been-saved-successfully"));

                return $this->redirect()->toRoute(
                    'zfcadmin/advisory-board/tender/details/general',
                    [
                        'id' => $tender->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form, 'type' => $tender]);
    }
}
