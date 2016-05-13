<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Organisation\Entity;
use Organisation\Form;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * @category    Organisation
 */
class OrganisationTypeController extends OrganisationAbstractController
{
    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $organisationQuery = $this->getOrganisationService()
            ->findEntitiesFiltered(Entity\Type::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        $form = new Form\OrganisationFilter($this->getOrganisationService());

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel([
            'paginator'     => $paginator,
            'form'          => $form,
            'encodedFilter' => urlencode($filterPlugin->getHash()),
            'order'         => $filterPlugin->getOrder(),
            'direction'     => $filterPlugin->getDirection(),
        ]);
    }

    /**
     * Create a new template.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        $data = array_merge($this->getRequest()->getPost()->toArray());

        $form = $this->getFormService()->prepare(Entity\Type::class, null, $data);
        $form->remove('delete');

        $form->setAttribute('class', 'form-horizontal');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                $this->redirect()->toRoute('zfcadmin/organisation-type/list');
            }

            if ($form->isValid()) {
                /* @var $type Entity\Type */
                $type = $form->getData();

                $result = $this->getOrganisationService()->newEntity($type);
                $this->redirect()->toRoute('zfcadmin/organisation-type/view', [
                    'id' => $result->getId(),
                ]);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * Create a new template.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $type = $this->getOrganisationService()->findEntityById(Entity\Type::class, $this->params('id'));

        if ($type->isEmpty()) {
            return $this->notFoundAction();
        }

        $data = array_merge($this->getRequest()->getPost()->toArray());

        $form = $this->getFormService()->prepare($type, $type, $data);
        $form->setAttribute('class', 'form-horizontal');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                $this->redirect()->toRoute('zfcadmin/organisation-type/view', [
                    'id' => $type->getId(),
                ]);
            }

            if ($form->isValid()) {
                /* @var $type Entity\Type */
                $type = $form->getData();

                $result = $this->getOrganisationService()->newEntity($type);
                $this->redirect()->toRoute('zfcadmin/organisation-type/view', [
                    'id' => $result->getId(),
                ]);
            }
        }

        return new ViewModel(['form' => $form]);
    }


    /**
     * @return array|ViewModel
     */
    public function viewAction()
    {
        $type = $this->getOrganisationService()->findEntityById(Entity\Type::class, $this->params('id'));

        if ($type->isEmpty()) {
            return $this->notFoundAction();
        }

        return new ViewModel(['type' => $type]);
    }
}
