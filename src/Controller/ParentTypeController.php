<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
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
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * Class ParentTypeController
 * @package Organisation\Controller
 */
class ParentTypeController extends OrganisationAbstractController
{
    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $organisationQuery = $this->getParentService()
            ->findEntitiesFiltered(Entity\Parent\Type::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
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

    /**
     * @return ViewModel
     */
    public function newAction()
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->getFormService()->prepare(Entity\Parent\Type::class, null, $data);
        $form->remove('delete');

        $form->setAttribute('class', 'form-horizontal');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                $this->redirect()->toRoute('zfcadmin/parent-type/list');
            }

            if ($form->isValid()) {
                /* @var $parentType Entity\Parent\Type */
                $parentType = $form->getData();

                $result = $this->getParentService()->newEntity($parentType);
                $this->redirect()->toRoute(
                    'zfcadmin/parent-type/view',
                    [
                        'id' => $result->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * @return ViewModel
     */
    public function editAction()
    {
        $parentType = $this->getParentService()->findEntityById(Entity\Parent\Type::class, $this->params('id'));

        if ($parentType->isEmpty()) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->getFormService()->prepare($parentType, $parentType, $data);
        $form->setAttribute('class', 'form-horizontal');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                $this->redirect()->toRoute(
                    'zfcadmin/parent-type/view',
                    [
                        'id' => $parentType->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                /* @var $parentType Entity\Parent\Type */
                $parentType = $form->getData();

                $result = $this->getParentService()->newEntity($parentType);
                $this->redirect()->toRoute(
                    'zfcadmin/parent-type/view',
                    [
                        'id' => $result->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }


    /**
     * @return array|ViewModel
     */
    public function viewAction()
    {
        $type = $this->getParentService()->findEntityById(Entity\Parent\Type::class, $this->params('id'));

        if (\is_null($type)) {
            return $this->notFoundAction();
        }

        return new ViewModel(['type' => $type]);
    }
}
