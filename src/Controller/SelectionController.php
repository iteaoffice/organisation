<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\Plugin\SelectionExport;
use Organisation\Entity\Selection;
use Organisation\Service\FormService;
use Organisation\Service\SelectionService;
use Search\Form\SearchFilter;
use Throwable;

/**
 * Class SelectionManagerController
 *
 * @package Organisation\Controller
 */
final class SelectionController extends AbstractController
{
    private SelectionService $selectionService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(
        SelectionService $selectionService,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->selectionService = $selectionService;
        $this->formService      = $formService;
        $this->translator       = $translator;
    }


    public function listAction(): ViewModel
    {
        $page              = $this->params()->fromRoute('page', 1);
        $filterPlugin      = $this->getOrganisationFilter();
        $organisationQuery = $this->selectionService->findFiltered(Selection::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SearchFilter();

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'        => $paginator,
                'form'             => $form,
                'encodedFilter'    => urlencode($filterPlugin->getHash()),
                'order'            => $filterPlugin->getOrder(),
                'direction'        => $filterPlugin->getDirection(),
                'selectionService' => $this->selectionService
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        $selection = $this->selectionService->findSelectionById((int)$this->params('id'));

        if (null === $selection) {
            return $this->notFoundAction();
        }

        try {
            $organisations = $this->selectionService->findOrganisationsInSelection($selection, true);

            $error = false;
        } catch (Throwable $e) {
            $organisations = [];
            $error         = $e->getMessage();
        }

        return new ViewModel(
            [
                'selectionService' => $this->selectionService,
                'selection'        => $selection,
                'organisations'    => $organisations,
                'error'            => $error,
            ]
        );
    }


    public function editSqlAction()
    {
        $selection = $this->selectionService->findSelectionById((int)$this->params('id'));

        if (null === $selection) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($selection, $data);
        $form->getInputFilter()->get('organisation_entity_selection')->get('sql')->setRequired(true);
        $form->getInputFilter()->get('organisation_entity_selection')->get('selection')->setRequired(false);
        $form->getInputFilter()->get('organisation_entity_selection')->get('contact')->setRequired(false);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/selection/view', ['id' => $selection->getId()]);
            }

            if ($form->isValid()) {
                $selection->setSql($data['organisation_entity_selection']['sql']);
                $this->selectionService->save($selection);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-query-of-organisation-selection-%s-has-been-updated-successfully'),
                        $selection->getSelection()
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/organisation/selection/view', ['id' => $selection->getId()]);
            }
        }

        return new ViewModel(
            [
                'selectionService' => $this->selectionService,
                'selection'        => $selection,
                'form'             => $form,
            ]
        );
    }

    public function editAction()
    {
        $selection = $this->selectionService->findSelectionById((int)$this->params('id'));

        if (null === $selection) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($selection, $data);

        if (! $this->selectionService->canDeleteSelection($selection)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/selection/view', ['id' => $selection->getId()]);
            }

            if (isset($data['delete']) && $this->selectionService->canDeleteSelection($selection)) {
                $this->selectionService->delete($selection);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-organisation-selection-%s-has-successfully-been-removed'),
                        $selection->getSelection()
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/organisation/selection/list');
            }

            if ($form->isValid()) {
                /**
                 * @var $selection Selection
                 */
                $selection = $form->getData();
                $this->selectionService->save($selection);

                return $this->redirect()->toRoute('zfcadmin/organisation/selection/view', ['id' => $selection->getId()]);
            }
        }

        return new ViewModel(
            [
                'form'             => $form,
                'selectionService' => $this->selectionService,
                'selection'        => $selection,
            ]
        );
    }

    public function newAction()
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(Selection::class, $data);
        $form->remove('delete');

        $form->get('organisation_entity_selection')->get('contact')->injectContact($this->identity());

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/selection/list');
            }

            if ($form->isValid()) {
                /** @var Selection $selection */
                $selection = $form->getData();
                $this->selectionService->save($selection);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-organisation-selection-%s-has-been-created-successfully'),
                        $selection->getSelection()
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/organisation/selection/view', ['id' => $selection->getId()]);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function copyAction()
    {
        $source = $this->selectionService->findSelectionById((int)$this->params('id'));

        if (null === $source) {
            return $this->notFoundAction();
        }

        $selection = clone $source;
        $data      = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare($selection, $data);
        $form->get('submit')->setValue($this->translator->translate('txt-copy'));
        $form->remove('delete');

        $form->get('organisation_entity_selection')->get('contact')->injectContact($this->identity());

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/selection/view', ['id' => $source->getId()]);
            }

            if ($form->isValid()) {
                /** @var Selection $selection */
                $selection = $form->getData();
                $selection->setId(null);
                $this->selectionService->duplicateSelection($selection, $source);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-organisation-selection-%s-has-been-copied-successfully'),
                        $selection->getSelection()
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/organisation/selection/view', ['id' => $selection->getId()]);
            }
        }

        return new ViewModel(['form' => $form]);
    }


    public function exportAction(): Response
    {
        $selection = $this->selectionService->findSelectionById((int)$this->params('id'));

        if (null === $selection) {
            $response = new Response();
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }


        $type = $this->params('type') === 'csv' ? SelectionExport::EXPORT_CSV : SelectionExport::EXPORT_EXCEL;

        return $this->organisationSelectionExport($selection, $type)->parseResponse();
    }
}
