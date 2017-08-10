<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use General\Entity\VatType;
use Organisation\Entity\Financial;
use Organisation\Form;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 *
 */
class OrganisationFinancialController extends OrganisationAbstractController
{
    /**
     * @return ViewModel
     */
    public function listAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $organisationQuery = $this->getOrganisationService()->findOrganisationFinancialList($filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\OrganisationFilter($this->getOrganisationService());

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
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        $organisation = $this->getOrganisationService()->findOrganisationById($this->params('id'));

        if (is_null($financial = $organisation->getFinancial())) {
            $financial = new Financial();
            $financial->setOrganisation($organisation);
        }

        $data = array_merge(
            [
                'vatType' => $financial->getVatType()->count() === 0 ? VatType::VAT_TYPE_LOCAL : $financial->getVatType()->first()->getId(),
            ],
            $this->getRequest()->getPost()->toArray()
        );

        $form = $this->getFormService()->prepare($financial, $financial, $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['delete'])) {
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-financial-organisation-of-%s-has-successfully-been-removed"),
                            $organisation
                        )
                    );

                $this->getOrganisationService()->removeEntity($financial);

                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $organisation->getId()],
                    ['fragment' => 'financial']
                );
            }

            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $organisation->getId()],
                    ['fragment' => 'financial']
                );
            }

            if ($form->isValid()) {
                /**
                 * @var $financial Financial
                 */
                $financial = $form->getData();

                //Force VAT to null when not given
                if (empty($data['organisation_entity_financial']['vat'])) {
                    $financial->setVat(null);
                }

                if ($data['vatType'] == 0) {
                    $financial->setVatType(null);
                } else {
                    $vatType = $this->getGeneralService()->findEntityById(VatType::class, $data['vatType']);
                    $arrayCollection = new ArrayCollection();
                    $arrayCollection->add($vatType);
                    $financial->setVatType($arrayCollection);
                }


                $this->getOrganisationService()->updateEntity($financial);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-financial-organisation-%s-has-successfully-been-updated"),
                            $organisation
                        )
                    );


                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $organisation->getId()],
                    ['fragment' => 'financial']
                );
            }
        }


        return new ViewModel(
            [
                'organisationService' => $this->getOrganisationService(),
                'organisation'        => $organisation,
                'financial'           => $financial,
                'form'                => $form,
            ]
        );
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
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\OrganisationFilter($this->getOrganisationService());

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'           => $paginator,
                'form'                => $form,
                'encodedFilter'       => urlencode($filterPlugin->getHash()),
                'order'               => $filterPlugin->getOrder(),
                'direction'           => $filterPlugin->getDirection(),
                'organisationService' => $this->getOrganisationService(),
            ]
        );
    }
}
