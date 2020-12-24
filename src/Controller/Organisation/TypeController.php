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

namespace Organisation\Controller\Organisation;

use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\OrganisationAbstractController;
use Organisation\Entity;
use Organisation\Form;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;

/**
 * Class TypeController
 * @package Organisation\Controller
 */
final class TypeController extends OrganisationAbstractController
{
    private OrganisationService $organisationService;
    private FormService $formService;

    public function __construct(OrganisationService $organisationService, FormService $formService)
    {
        $this->organisationService = $organisationService;
        $this->formService         = $formService;
    }

    public function listAction(): ViewModel
    {
        $page              = $this->params()->fromRoute('page', 1);
        $filterPlugin      = $this->getOrganisationFilter();
        $organisationQuery = $this->organisationService
            ->findFiltered(Entity\Type::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\OrganisationFilter($this->organisationService);

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

        $form = $this->formService->prepare(Entity\Type::class, $data);
        $form->remove('delete');


        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/type/list');
            }

            if ($form->isValid()) {
                /* @var $type Entity\Type */
                $type = $form->getData();

                $result = $this->organisationService->save($type);
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/type/view',
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
        $type = $this->organisationService->find(Entity\Type::class, (int)$this->params('id'));

        if (null === $type) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($type, $data);

        if (! $this->organisationService->canDeleteType($type)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/type/view',
                    [
                        'id' => $type->getId(),
                    ]
                );
            }

            if (isset($data['delete']) && $this->organisationService->canDeleteOrganisation($type)) {
                $this->organisationService->delete($type);
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/type/list',
                    [
                        'id' => $type->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                /* @var $type Entity\Type */
                $type = $form->getData();

                $this->organisationService->save($type);
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/type/view',
                    [
                        'id' => $type->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function viewAction(): ViewModel
    {
        $type = $this->organisationService->find(Entity\Type::class, (int)$this->params('id'));

        if (null === $type) {
            return $this->notFoundAction();
        }

        return new ViewModel(['type' => $type]);
    }
}
