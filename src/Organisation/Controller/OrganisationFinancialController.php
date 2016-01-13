<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Organisation\Entity\Financial;
use Organisation\Form\OrganisationFilter;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 *
 */
class OrganisationFinancialController extends OrganisationAbstractController
{
    /**
     *
     */
    public function listAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $organisationQuery = $this->getOrganisationService()->findOrganisationFinancialList($filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 15);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        $form = new OrganisationFilter($this->getOrganisationService());

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
     * @return ViewModel
     */
    public function editAction()
    {
        $organisationService = $this->getOrganisationService()->setOrganisationId($this->params('id'));

        if (is_null($financial = $organisationService->getOrganisation()->getFinancial())) {
            $financial = new Financial();
            $financial->setOrganisation($organisationService->getOrganisation());
        }

        $data = array_merge([
            'vatType' => ($financial->getVatType()->count() == 0 ? 0 : $financial->getVatType()->first()->getId())
        ], $this->getRequest()->getPost()->toArray());

        $form = $this->getFormService()->prepare($financial, $financial, $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['delete'])) {
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        $this->translate("txt-financial-organisation-of-%s-has-successfully-been-removed"),
                        $organisationService->getOrganisation()
                    ));

                $this->getOrganisationService()->removeEntity($financial);

                return $this->redirect()
                    ->toRoute(
                        'zfcadmin/organisation/view',
                        ['id' => $organisationService->getOrganisation()->getId()],
                        ['fragment' => 'financial']
                    );
            }

            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute(
                        'zfcadmin/organisation/view',
                        ['id' => $organisationService->getOrganisation()->getId()],
                        ['fragment' => 'financial']
                    );
            }

            if ($form->isValid()) {
                /**
                 * @var $financial Financial
                 */
                $financial = $form->getData();

                if ($data['vatType'] == 0) {
                    $financial->setVatType(null);
                } else {
                    $vatType = $this->getGeneralService()->findEntityById('vatType', $data['vatType']);
                    $arrayCollection = new ArrayCollection();
                    $arrayCollection->add($vatType);
                    $financial->setVatType($arrayCollection);
                }


                $this->getOrganisationService()->updateEntity($financial);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        $this->translate("txt-financial-organisation-%s-has-successfully-been-updated"),
                        $organisationService->getOrganisation()
                    ));


                return $this->redirect()
                    ->toRoute(
                        'zfcadmin/organisation/view',
                        ['id' => $organisationService->getOrganisation()->getId()],
                        ['fragment' => 'financial']
                    );
            }
        }


        return new ViewModel([
            'organisationService' => $organisationService,
            'financial'           => $financial,
            'form'                => $form,
        ]);
    }


    /**
     * @return ViewModel
     */
    public function noFinancialAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $organisationQuery = $this->getOrganisationService()
            ->findActiveOrganisationWithoutFinancial($filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 15);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        $form = new OrganisationFilter($this->getOrganisationService());

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel([
            'paginator'     => $paginator,
            'form'          => $form,
            'encodedFilter' => urlencode($filterPlugin->getHash()),
            'order'         => $filterPlugin->getOrder(),
            'direction'     => $filterPlugin->getDirection(),
        ]);
    }
}
