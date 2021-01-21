<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\Parent;

use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Entity;
use Organisation\Service\FormService;
use Organisation\Service\ParentService;
use Search\Form\SearchFilter;

/**
 * Class TypeController
 * @package Organisation\Controller\Parent
 */
final class TypeController extends AbstractController
{
    private ParentService $parentService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(ParentService $parentService, FormService $formService, TranslatorInterface $translator)
    {
        $this->parentService = $parentService;
        $this->formService   = $formService;
        $this->translator    = $translator;
    }

    public function listAction(): ViewModel
    {
        $page              = $this->params()->fromRoute('page', 1);
        $filterPlugin      = $this->getOrganisationFilter();
        $organisationQuery = $this->parentService->findFiltered(Entity\Parent\Type::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SearchFilter();
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

    public function newAction()
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(Entity\Parent\Type::class, $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/parent/type/list');
            }

            if ($form->isValid()) {
                /* @var $parentType Entity\Parent\Type */
                $parentType = $form->getData();

                $result = $this->parentService->save($parentType);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-parent-type-has-been-created-successfully'),
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/type/view',
                    [
                        'id' => $result->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }


    public function editAction()
    {
        $parentType = $this->parentService->find(Entity\Parent\Type::class, (int)$this->params('id'));

        if (null === $parentType) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($parentType, $data);

        if (! $this->parentService->canDeleteType($parentType)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/parent/type/view',
                    [
                        'id' => $parentType->getId(),
                    ]
                );
            }

            if (isset($data['delete']) && $this->parentService->canDeleteType($parentType)) {
                $this->parentService->delete($parentType);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-parent-type-has-been-deleted-successfully'),
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/type/list'
                );
            }

            if ($form->isValid()) {
                /* @var $parentType Entity\Parent\Type */
                $parentType = $form->getData();

                $result = $this->parentService->save($parentType);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-parent-type-has-been-updated-successfully'),
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/type/view',
                    [
                        'id' => $result->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }


    public function viewAction(): ViewModel
    {
        $type = $this->parentService->find(Entity\Parent\Type::class, (int)$this->params('id'));

        if (null === $type) {
            return $this->notFoundAction();
        }

        return new ViewModel(['type' => $type]);
    }
}
