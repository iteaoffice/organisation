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

use Contact\Entity\Address;
use Contact\Entity\AddressType;
use Contact\Service\ContactService;
use General\Entity\Country;
use General\Service\CountryService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Entity;
use Organisation\Form;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;
use Project\Service\ProjectService;

/**
 * Class FinancialController
 * @package Organisation\Controller\Parent
 */
final class FinancialController extends AbstractController
{
    private ParentService $parentService;
    private ContactService $contactService;
    private ProjectService $projectService;
    private CountryService $countryService;
    private OrganisationService $organisationService;
    private TranslatorInterface $translator;

    public function __construct(
        ParentService $parentService,
        ContactService $contactService,
        ProjectService $projectService,
        CountryService $countryService,
        OrganisationService $organisationService,
        TranslatorInterface $translator
    ) {
        $this->parentService       = $parentService;
        $this->contactService      = $contactService;
        $this->projectService      = $projectService;
        $this->countryService      = $countryService;
        $this->organisationService = $organisationService;
        $this->translator          = $translator;
    }

    public function newAction()
    {
        $parent = $this->parentService->findParentById((int)$this->params('parentId'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $formData = [
            'preferredDelivery' => Entity\Financial::EMAIL_DELIVERY,
            'omitContact'       => Entity\Financial::OMIT_CONTACT,
        ];

        $financialAddress = null;

        $form = new Form\Parent\FinancialForm(
            $parent,
            $this->countryService,
            $this->organisationService
        );

        $formData['attention'] = $parent->getContact()->getDisplayName();
        $formData['contact']   = $parent->getContact()->getId();

        if (
            null !== (
                $financialAddress = $this->contactService->getFinancialAddress(
                    $parent->getContact()
                )
            )
        ) {
            $formData['address'] = $financialAddress->getAddress();
            $formData['zipCode'] = $financialAddress->getZipCode();
            $formData['city']    = $financialAddress->getCity();
            $formData['country'] = $financialAddress->getCountry()->getId();
        }


        $data = array_merge($formData, $this->getRequest()->getPost()->toArray());

        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/parent/details/financial',
                    ['id' => $parent->getId(),],
                );
            }

            if ($form->isValid()) {
                $formData = $form->getData();

                /** @var Entity\Financial $financialOrganisation */
                $financialOrganisation = $this->organisationService->find(
                    Entity\Financial::class,
                    (int)$formData['organisationFinancial']
                );

                $financial = new Entity\Parent\Financial();
                $financial->setParent($parent);
                $financial->setContact($this->contactService->findContactById((int)$formData['contact']));
                $financial->setOrganisation($financialOrganisation->getOrganisation());
                $financial->setBranch($formData['branch']);
                $this->parentService->save($financial);

                /*
                 * save the financial address
                 */

                if (
                    null === (
                        $financialAddress = $this->contactService->getFinancialAddress($financial->getContact())
                    )
                ) {
                    $financialAddress = new Address();
                    $financialAddress->setContact($financial->getContact());
                    /**
                     * @var $addressType AddressType
                     */
                    $addressType = $this->contactService
                        ->find(AddressType::class, AddressType::ADDRESS_TYPE_FINANCIAL);
                    $financialAddress->setType($addressType);
                }
                $financialAddress->setAddress($formData['address']);
                $financialAddress->setZipCode($formData['zipCode']);
                $financialAddress->setCity($formData['city']);
                /**
                 * @var Country $country
                 */
                $country = $this->countryService->find(Country::class, (int)$formData['country']);
                $financialAddress->setCountry($country);
                $this->contactService->save($financialAddress);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate(
                            'txt-financial-organisation-for-parent-%s-has-successfully-been-created'
                        ),
                        $financial->getParent()
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/details/financial',
                    ['id' => $financial->getParent()->getId()]
                );
            }
        }

        return new ViewModel(
            [
                'parent'         => $parent,
                'parentService'  => $this->parentService,
                'projectService' => $this->projectService,
                'form'           => $form,
            ]
        );
    }

    public function editAction()
    {
        /** @var Entity\Parent\Financial $financial */
        $financial = $this->parentService->find(Entity\Parent\Financial::class, (int)$this->params('id'));

        if (null === $financial) {
            return $this->notFoundAction();
        }

        $formData = [
            'preferredDelivery' => Entity\Financial::EMAIL_DELIVERY,
            'omitContact'       => Entity\Financial::OMIT_CONTACT,
            'branch'            => $financial->getBranch()
        ];

        $financialAddress = null;

        $form = new Form\Parent\FinancialForm(
            $financial->getParent(),
            $this->countryService,
            $this->organisationService
        );

        if (null !== $financial->getOrganisation()->getFinancial()) {
            $formData['organisationFinancial'] = $financial->getOrganisation()->getFinancial()->getId();
        }
        $formData['attention'] = $financial->getContact()->getDisplayName();
        $formData['contact']   = $financial->getContact()->getId();
        //$form->get('contact')->injectContact($financial->getContact());

        //Try to find the financial address
        $financialAddress = $this->contactService->getFinancialAddress($financial->getContact());

        if (null !== $financialAddress) {
            $formData['address'] = $financialAddress->getAddress();
            $formData['zipCode'] = $financialAddress->getZipCode();
            $formData['city']    = $financialAddress->getCity();
            $formData['country'] = $financialAddress->getCountry()->getId();
        }

        $data = array_merge($formData, $this->getRequest()->getPost()->toArray());

        $form->setData($data);


        if ($this->getRequest()->isPost()) {
            if (isset($data['delete'])) {
                $this->parentService->delete($financial);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate(
                            'txt-financial-organisation-of-parent-%s-has-successfully-been-deleted'
                        ),
                        $financial->getParent()
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/details/financial',
                    ['id' => $financial->getParent()->getId()]
                );
            }

            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/parent/details/financial',
                    ['id' => $financial->getParent()->getId()]
                );
            }

            if ($form->isValid()) {
                $formData = $form->getData();

                /** @var Entity\Financial $financialOrganisation */
                $financialOrganisation = $this->organisationService->find(
                    Entity\Financial::class,
                    (int)$formData['organisationFinancial']
                );

                $financial->setContact($this->contactService->findContactById((int)$formData['contact']));
                $financial->setOrganisation($financialOrganisation->getOrganisation());
                $financial->setBranch($formData['branch']);
                $this->parentService->save($financial);

                /*
                 * save the financial address
                 */
                $financialAddress
                    = $financialAddress = $this->contactService->getFinancialAddress($financial->getContact());

                if (null === $financialAddress) {
                    $financialAddress = new Address();
                    $financialAddress->setContact($financial->getContact());
                    /**
                     * @var $addressType AddressType
                     */
                    $addressType = $this->contactService
                        ->find(AddressType::class, AddressType::ADDRESS_TYPE_FINANCIAL);
                    $financialAddress->setType($addressType);
                }
                $financialAddress->setAddress($formData['address']);
                $financialAddress->setZipCode($formData['zipCode']);
                $financialAddress->setCity($formData['city']);
                /**
                 * @var Country $country
                 */
                $country = $this->countryService->find(Country::class, (int)$formData['country']);
                $financialAddress->setCountry($country);
                $this->contactService->save($financialAddress);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-parent-%s-has-successfully-been-updated'),
                        $financial->getParent()
                    )
                );
            }

            return $this->redirect()->toRoute(
                'zfcadmin/parent/details/financial',
                ['id' => $financial->getParent()->getId()]
            );
        }

        return new ViewModel(
            [
                'parent'         => $financial->getParent(),
                'parentService'  => $this->parentService,
                'projectService' => $this->projectService,
                'form'           => $form,
            ]
        );
    }
}
