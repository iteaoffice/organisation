<?php

/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Organisation\Entity;
use Organisation\Form;
use Organisation\Service\FormService;
use Organisation\Service\ParentService;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;

/**
 * Class ParentTypeController
 *
 * @package Organisation\Controller
 */
final class ParentTypeController extends OrganisationAbstractController
{
    private ParentService $parentService;
    private FormService $formService;

    public function __construct(ParentService $parentService, FormService $formService)
    {
        $this->parentService = $parentService;
        $this->formService = $formService;
    }

    public function listAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $organisationQuery = $this->parentService->findFiltered(Entity\Parent\Type::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\ParentTypeFilter();

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
                return $this->redirect()->toRoute('zfcadmin/parent-type/list');
            }

            if ($form->isValid()) {
                /* @var $parentType Entity\Parent\Type */
                $parentType = $form->getData();

                $result = $this->parentService->save($parentType);
                return $this->redirect()->toRoute(
                    'zfcadmin/parent-type/view',
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

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/parent-type/view',
                    [
                        'id' => $parentType->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                /* @var $parentType Entity\Parent\Type */
                $parentType = $form->getData();

                $result = $this->parentService->save($parentType);
                return $this->redirect()->toRoute(
                    'zfcadmin/parent-type/view',
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
