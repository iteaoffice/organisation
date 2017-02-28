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
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\Controller;

use Contact\Entity\Address;
use Contact\Entity\AddressType;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use General\Entity\Country;
use Organisation\Entity;
use Organisation\Entity\Financial;
use Organisation\Form;
use Zend\Paginator\Paginator;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * @category    Parent
 */
class ParentController extends OrganisationAbstractController
{

    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $query = $this->getParentService()->findEntitiesFiltered(
            Entity\OParent::class,
            $filterPlugin->getFilter()
        );

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($query, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\ParentFilter($this->getParentService());

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
     * Create a new template.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        $organisation = null;
        if (!is_null($this->params('organisationId'))) {
            $organisation = $this->getOrganisationService()->findOrganisationById($this->params('organisationId'));
        }

        $data = array_merge($this->getRequest()->getPost()->toArray());

        $parent = new Entity\OParent();
        $form = $this->getFormService()->prepare($parent, null, $data);
        $form->remove('delete');

        if (!is_null($organisation)) {
            //Inject the organisation in the form
            $form->get($parent->get('underscore_entity_name'))->get('organisation')
                ->setValueOptions([$organisation->getId() => $organisation->getOrganisation()]);

            $contactsInOrganisation = [];
            foreach ($this->getContactService()->findContactsInOrganisation($organisation) as $contact) {
                $contactsInOrganisation[$contact->getId()] = $contact->getFormName();
            }
            asort($contactsInOrganisation);

            //Inject the organisation in the form
            $form->get($parent->get('underscore_entity_name'))->get('contact')
                ->setValueOptions($contactsInOrganisation);
        }


        $form->setAttribute('class', 'form-horizontal');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                $this->redirect()->toRoute('zfcadmin/parent/list');
            }

            if ($form->isValid()) {
                /* @var $parent Entity\OParent */
                $parent = $form->getData();

                $parent->setDateParentTypeUpdate(new \DateTime());

                $result = $this->getParentService()->newEntity($parent);
                $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    [
                        'id' => $result->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * Create a new template.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addOrganisationAction()
    {
        $parent = $this->getParentService()->findParentById($this->params('id'));

        $organisation = null;
        if (!is_null($this->params('organisationId'))) {
            $organisation = $this->getOrganisationService()->findOrganisationById($this->params('organisationId'));
        }

        $data = array_merge($this->getRequest()->getPost()->toArray());

        $form = new Form\AddOrganisation();

        if (!is_null($organisation)) {
            //Inject the organisation in the form
            $form->get('organisation')->setValueOptions([$organisation->getId() => $organisation->getOrganisation()]);

            $contactsInOrganisation = [];
            foreach ($this->getContactService()->findContactsInOrganisation($organisation) as $contact) {
                $contactsInOrganisation[$contact->getId()] = $contact->getFormName();
            }
            asort($contactsInOrganisation);

            //Inject the organisation in the form
            $form->get('contact')->setValueOptions($contactsInOrganisation);
        }

        $form->setAttribute('class', 'form-horizontal');
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    [
                        'id' => $parent->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                $parentOrganisation = new Entity\Parent\Organisation();
                $parentOrganisation->setParent($parent);

                //Find the organiation from the form
                $organisation = $this->getOrganisationService()->findOrganisationById($data['organisation']);
                $parentOrganisation->setOrganisation($organisation);

                //Find the contact from the form.
                $contact = $this->getContactService()->findContactById($data['contact']);
                $parentOrganisation->setContact($contact);


                $parentOrganisation = $this->getParentService()->newEntity($parentOrganisation);
                $this->redirect()->toRoute(
                    'zfcadmin/parent/organisation/view',
                    [
                        'id' => $parentOrganisation->getId(),
                    ]
                );
            }
        }

        return new ViewModel(
            [
                'form'   => $form,
                'parent' => $parent,
            ]
        );
    }

    /**
     * @return array|ViewModel
     */
    public function editAction()
    {
        $parent = $this->getParentService()->findParentById($this->params('id'));

        if (is_null($parent)) {
            return $this->notFoundAction();
        }

        $currentParentType = $parent->getType()->getId();

        $data = array_merge($this->getRequest()->getPost()->toArray());
        $form = $this->getFormService()->prepare($parent, $parent, $data);

        $form->get($parent->get('underscore_entity_name'))->get('contact')->injectContact($parent->getContact());
        $form->get($parent->get('underscore_entity_name'))->get('organisation')
            ->injectOrganisation($parent->getOrganisation());

        $form->setAttribute('class', 'form-horizontal');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                $this->redirect()->toRoute('zfcadmin/parent/list');
            }

            if ($form->isValid()) {
                /* @var $parent Entity\OParent */
                $parent = $form->getData();

                if ($parent->getType()->getId() !== $currentParentType) {
                    $parent->setDateParentTypeUpdate(new \DateTime());
                }

                $result = $this->getParentService()->newEntity($parent);
                $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    [
                        'id' => $result->getId(),
                    ]
                );
            }
        }

        return new ViewModel([
            'form'   => $form,
            'parent' => $parent,
        ]);
    }


    /**
     * @return array|ViewModel
     */
    public function viewAction()
    {
        $parent = $this->getParentService()->findParentById($this->params('id'));

        if (is_null($parent)) {
            return $this->notFoundAction();
        }

        $year = date("Y");

        return new ViewModel(
            [
                'parent'              => $parent,
                'organisationService' => $this->getOrganisationService(),
                'contactService'      => $this->getContactService(),
                'year'                => $year,
                'invoiceFactor'       => $this->getParentService()->parseInvoiceFactor($parent, $year),
            ]
        );
    }

    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function editFinancialAction()
    {
        $parent = $this->getParentService()->findParentById($this->params('id'));

        if (is_null($parent)) {
            return $this->notFoundAction();
        }

        $formData = [
            'preferredDelivery' => \Organisation\Entity\Financial::EMAIL_DELIVERY,
            'omitContact'       => \Organisation\Entity\Financial::OMIT_CONTACT,
        ];

        $financialAddress = null;

        $form = new Form\Financial($parent, $this->getGeneralService(), $this->getOrganisationService());

        if (!is_null($parent->getFinancial())) {
            $branch = $parent->getFinancial()->getBranch();
            $formData['attention'] = $parent->getFinancial()->getContact()->getDisplayName();

            $form->get('contact')->injectContact($parent->getFinancial()->getContact());

            if (!is_null(
                $financialAddress = $this->getContactService()->getFinancialAddress(
                    $parent->getFinancial()
                        ->getContact()
                )
            )
            ) {
                $formData['address'] = $financialAddress->getAddress();
                $formData['zipCode'] = $financialAddress->getZipCode();
                $formData['city'] = $financialAddress->getCity();
                $formData['country'] = $financialAddress->getCountry()->getId();
            }
        }

        $data = array_merge($formData, $this->getRequest()->getPost()->toArray());

        $form->setData($data);


        if ($this->getRequest()->isPost() && $form->isValid()) {
            $formData = $form->getData();

            /** @var Financial $financialOrganisation */
            $financialOrganisation = $this->getOrganisationService()
                ->findEntityById(Financial::class, $formData['organisationFinancial']);

            /**
             *
             * Update the parentFinancial
             */
            $parentFinancial = $parent->getFinancial();
            if (is_null($parentFinancial)) {
                $parentFinancial = new Entity\Parent\Financial();
                $parentFinancial->setParent($parent);
            }
            $parentFinancial->setContact($this->getContactService()->findContactById($formData['contact']));
            $parentFinancial->setOrganisation($financialOrganisation->getOrganisation());
            $parentFinancial->setBranch($formData['branch']);
            $this->getParentService()->updateEntity($parentFinancial);

            /*
             * save the financial address
             */

            if (is_null(
                $financialAddress = $this->getContactService()->getFinancialAddress($parentFinancial->getContact())
            )) {
                $financialAddress = new Address();
                $financialAddress->setContact($parent->getFinancial()->getContact());
                /**
                 * @var $addressType AddressType
                 */
                $addressType = $this->getContactService()
                    ->findEntityById(AddressType::class, AddressType::ADDRESS_TYPE_FINANCIAL);
                $financialAddress->setType($addressType);
            }
            $financialAddress->setAddress($formData['address']);
            $financialAddress->setZipCode($formData['zipCode']);
            $financialAddress->setCity($formData['city']);
            /**
             * @var Country $country
             */
            $country = $this->getGeneralService()->findEntityById(Country::class, $formData['country']);
            $financialAddress->setCountry($country);
            $this->getContactService()->updateEntity($financialAddress);
            $this->flashMessenger()->setNamespace('success')
                ->addMessage(sprintf($this->translate("txt-parent-%s-has-successfully-been-updated"), $parent));

            return $this->redirect()->toRoute(
                'zfcadmin/parent/view',
                [
                    'id' => $parent->getId(),
                ],
                [
                    'fragment' => 'financial',
                ]
            );
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
     * @return ViewModel
     */
    public function overviewVariableContributionAction()
    {
        $parent = $this->getParentService()->findParentById($this->params('id'));

        if (is_null($parent)) {
            return $this->notFoundAction();
        }

        $year = (int)$this->params('year');
        $period = (int)$this->params('period');

        return new ViewModel(
            [
                'year'          => $year,
                'period'        => $period,
                'parent'        => $parent,
                'invoiceFactor' => $this->getParentService()->parseInvoiceFactor($parent, $year)

            ]
        );
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function overviewVariableContributionPdfAction()
    {
        $parent = $this->getParentService()->findParentById($this->params('id'));

        if (is_null($parent)) {
            return $this->notFoundAction();
        }

        $year = (int)$this->params('year');
        $period = (int)$this->params('period');


        $renderPaymentSheet = $this->renderOverviewVariableContributionSheet($parent, $year, $period);
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")->addHeaderLine("Pragma: public")
            ->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="' . sprintf(
                    "overview_variable_contribution_%s_%s_%sH.pdf",
                    $parent->getOrganisation()->getDocRef(),
                    $year,
                    $period
                ) . '"'
            )
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', strlen($renderPaymentSheet->getPDFData()));
        $response->setContent($renderPaymentSheet->getPDFData());

        return $response;
    }

    /**
     * @return ViewModel
     */
    public function overviewExtraVariableContributionAction()
    {
        $parent = $this->getParentService()->findParentById($this->params('id'));

        if (is_null($parent)) {
            return $this->notFoundAction();
        }

        $year = (int)$this->params('year');
        $period = (int)$this->params('period');

        return new ViewModel(
            [
                'year'   => $year,
                'period' => $period,
                'parent' => $parent,

            ]
        );
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function overviewExtraVariableContributionPdfAction()
    {
        $parent = $this->getParentService()->findParentById($this->params('id'));

        if (is_null($parent)) {
            return $this->notFoundAction();
        }

        $year = (int)$this->params('year');
        $period = (int)$this->params('period');


        $renderPaymentSheet = $this->renderOverviewExtraVariableContributionSheet($parent, $year, $period);
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")->addHeaderLine("Pragma: public")
            ->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="' . sprintf(
                    "overview_extra_variable_contribution_%s_%s_%sH.pdf",
                    $parent->getOrganisation()->getDocRef(),
                    $year,
                    $period
                ) . '"'
            )
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', strlen($renderPaymentSheet->getPDFData()));
        $response->setContent($renderPaymentSheet->getPDFData());

        return $response;
    }

    /**
     * @return ViewModel
     */
    public function ImportParentAction()
    {
        set_time_limit(0);

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new Form\Import();
        $form->setData($data);

        /** store the data in the session, so we can use it when we really handle the import */
        $importSession = new Container('import');

        $handleImport = null;
        if ($this->getRequest()->isPost()) {
            if (isset($data['upload']) && $form->isValid()) {
                $fileData = file_get_contents($data['file']['tmp_name'], FILE_TEXT);

                $importSession->active = true;
                $importSession->fileData = $fileData;

                $handleImport = $this->handleParentImport(
                    $fileData,
                    [],
                    false
                );
            }

            if (isset($data['import'], $data['key']) && $importSession->active) {
                $handleImport = $this->handleParentImport(
                    $importSession->fileData,
                    $data['key'],
                    true
                );
            }
        }

        return new ViewModel(['form'           => $form,
                              'handleImport'   => $handleImport,
                              'contactService' => $this->getContactService()
        ]);
    }

    /**
     * @return ViewModel
     */
    public function ImportProjectAction()
    {
        set_time_limit(0);

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new Form\Import();
        $form->setData($data);

        /** store the data in the session, so we can use it when we really handle the import */
        $importSession = new Container('import');

        $handleImport = null;
        if ($this->getRequest()->isPost()) {
            if (isset($data['upload']) && $form->isValid()) {
                $fileData = file_get_contents($data['file']['tmp_name'], FILE_TEXT);

                $importSession->active = true;
                $importSession->fileData = $fileData;

                $handleImport = $this->handleParentAndProjectImport(
                    $fileData,
                    [],
                    false
                );
            }

            if (isset($data['import'], $data['key']) && $importSession->active) {
                $handleImport = $this->handleParentAndProjectImport(
                    $importSession->fileData,
                    $data['key'],
                    true
                );
            }
        }

        return new ViewModel(['form' => $form, 'handleImport' => $handleImport]);
    }
}
