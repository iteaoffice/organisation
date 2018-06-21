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

use Contact\Entity\Address;
use Contact\Entity\AddressType;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use General\Entity\Country;
use Organisation\Entity;
use Organisation\Form;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * @category    Parent
 */
class ParentFinancialController extends OrganisationAbstractController
{

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction()
    {
        /** @var Entity\OParent $parent */
        $parent = $this->getParentService()->findParentById((int) $this->params('parentId'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $formData = [
            'preferredDelivery' => Entity\Financial::EMAIL_DELIVERY,
            'omitContact'       => Entity\Financial::OMIT_CONTACT,
        ];

        $financialAddress = null;

        $form = new Form\Financial(
            $parent,
            $this->getGeneralService(),
            $this->getOrganisationService()
        );

        $formData['attention'] = $parent->getContact()->getDisplayName();
        $formData['contact'] = $parent->getContact()->getId();
        $form->get('contact')->injectContact($parent->getContact());

        if (!\is_null(
            $financialAddress = $this->getContactService()->getFinancialAddress(
                $parent->getContact()
            )
        )
        ) {
            $formData['address'] = $financialAddress->getAddress();
            $formData['zipCode'] = $financialAddress->getZipCode();
            $formData['city'] = $financialAddress->getCity();
            $formData['country'] = $financialAddress->getCountry()->getId();
        }


        $data = array_merge($formData, $this->getRequest()->getPost()->toArray());

        $form->setData($data);


        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $parent->getId(),],
                    ['fragment' => 'financial',]
                );
            }

            if ($form->isValid()) {
                $formData = $form->getData();

                /** @var Entity\Financial $financialOrganisation */
                $financialOrganisation = $this->getOrganisationService()->findEntityById(
                    Entity\Financial::class,
                    $formData['organisationFinancial']
                );

                $financial = new Entity\Parent\Financial();
                $financial->setParent($parent);
                $financial->setContact($this->getContactService()->findContactById($formData['contact']));
                $financial->setOrganisation($financialOrganisation->getOrganisation());
                $financial->setBranch($formData['branch']);
                $this->getParentService()->updateEntity($financial);

                /*
                 * save the financial address
                 */

                if (\is_null(
                    $financialAddress = $this->getContactService()->getFinancialAddress($financial->getContact())
                )
                ) {
                    $financialAddress = new Address();
                    $financialAddress->setContact($financial->getContact());
                    /**
                     * @var $addressType AddressType
                     */
                    $addressType = $this->getContactService()
                        ->find(AddressType::class, AddressType::ADDRESS_TYPE_FINANCIAL);
                    $financialAddress->setType($addressType);
                }
                $financialAddress->setAddress($formData['address']);
                $financialAddress->setZipCode($formData['zipCode']);
                $financialAddress->setCity($formData['city']);
                /**
                 * @var Country $country
                 */
                $country = $this->generalService->find(Country::class, (int) $formData['country']);
                $financialAddress->setCountry($country);
                $this->getContactService()->save($financialAddress);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-financial-organisation-for-parent-%s-has-successfully-been-created"),
                            $financial->getParent()
                        )
                    );

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $financial->getParent()->getId(),],
                    ['fragment' => 'financial',]
                );
            }
        }

        return new ViewModel(
            [
                'parent'         => $parent,
                'parentService'  => $this->getParentService(),
                'projectService' => $this->getProjectService(),
                'form'           => $form,
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        /** @var Entity\Parent\Financial $financial */
        $financial = $this->getParentService()->findEntityById(Entity\Parent\Financial::class, $this->params('id'));

        if (\is_null($financial)) {
            return $this->notFoundAction();
        }

        $formData = [
            'preferredDelivery' => Entity\Financial::EMAIL_DELIVERY,
            'omitContact'       => Entity\Financial::OMIT_CONTACT,
        ];

        $financialAddress = null;

        $form = new Form\Financial(
            $financial->getParent(),
            $this->getGeneralService(),
            $this->getOrganisationService()
        );

        if (null !== $financial->getOrganisation()->getFinancial()) {
            $formData['organisationFinancial'] = $financial->getOrganisation()->getFinancial()->getId();
        }
        $formData['attention'] = $financial->getContact()->getDisplayName();
        $formData['contact'] = $financial->getContact()->getId();
        $form->get('contact')->injectContact($financial->getContact());

        //Try to find the financial address
        $financialAddress = $this->getContactService()->getFinancialAddress($financial->getContact());

        if (null !== $financialAddress) {
            $formData['address'] = $financialAddress->getAddress();
            $formData['zipCode'] = $financialAddress->getZipCode();
            $formData['city'] = $financialAddress->getCity();
            $formData['country'] = $financialAddress->getCountry()->getId();
        }


        $data = array_merge($formData, $this->getRequest()->getPost()->toArray());

        $form->setData($data);


        if ($this->getRequest()->isPost()) {
            if (isset($data['delete'])) {
                $this->getParentService()->removeEntity($financial);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-financial-organisation-of-parent-%s-has-successfully-been-deleted"),
                            $financial->getParent()
                        )
                    );

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $financial->getParent()->getId(),],
                    ['fragment' => 'financial',]
                );
            }

            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $financial->getParent()->getId(),],
                    ['fragment' => 'financial',]
                );
            }

            if ($form->isValid()) {
                $formData = $form->getData();

                /** @var Entity\Financial $financialOrganisation */
                $financialOrganisation = $this->getOrganisationService()->findEntityById(
                    Entity\Financial::class,
                    $formData['organisationFinancial']
                );

                $financial->setContact($this->getContactService()->findContactById($formData['contact']));
                $financial->setOrganisation($financialOrganisation->getOrganisation());
                $financial->setBranch($formData['branch']);
                $this->getParentService()->updateEntity($financial);

                /*
                 * save the financial address
                 */
                $financialAddress
                    = $financialAddress = $this->getContactService()->getFinancialAddress($financial->getContact());

                if (null === $financialAddress) {
                    $financialAddress = new Address();
                    $financialAddress->setContact($financial->getContact());
                    /**
                     * @var $addressType AddressType
                     */
                    $addressType = $this->getContactService()
                        ->find(AddressType::class, AddressType::ADDRESS_TYPE_FINANCIAL);
                    $financialAddress->setType($addressType);
                }
                $financialAddress->setAddress($formData['address']);
                $financialAddress->setZipCode($formData['zipCode']);
                $financialAddress->setCity($formData['city']);
                /**
                 * @var Country $country
                 */
                $country = $this->generalService->find(Country::class, (int) $formData['country']);
                $financialAddress->setCountry($country);
                $this->getContactService()->save($financialAddress);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-parent-%s-has-successfully-been-updated"),
                            $financial->getParent()
                        )
                    );
            }

            return $this->redirect()->toRoute(
                'zfcadmin/parent/view',
                ['id' => $financial->getParent()->getId(),],
                ['fragment' => 'financial',]
            );
        }

        return new ViewModel(
            [
                'parent'         => $financial->getParent(),
                'parentService'  => $this->getParentService(),
                'projectService' => $this->getProjectService(),
                'form'           => $form,
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function noFinancialAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $parentQuery = $this->getParentService()
            ->findActiveParentWithoutFinancial($filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($parentQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\ParentFilter($this->getParentService());

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
