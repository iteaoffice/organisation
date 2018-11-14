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

use Affiliation\Entity\Affiliation;
use Affiliation\Service\AffiliationService;
use Affiliation\Service\DoaService;
use Affiliation\Service\LoiService;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use General\Service\GeneralService;
use Invoice\Entity\Invoice;
use Invoice\Form\InvoiceFilter;
use Invoice\Service\InvoiceService;
use Organisation\Entity\Logo;
use Organisation\Entity\Organisation;
use Organisation\Entity\Web;
use Organisation\Form\AddAffiliation;
use Organisation\Form\ManageWeb;
use Organisation\Form\OrganisationFilter;
use Organisation\Form\OrganisationMerge;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;
use Zend\Form\Fieldset;
use Zend\Http\Request;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;
use Zend\View\Model\ViewModel;

/**
 * Class OrganisationAdminController
 *
 * @package Organisation\Controller
 */
final class OrganisationAdminController extends OrganisationAbstractController
{
    /**
     * @var OrganisationService
     */
    private $organisationService;
    /**
     * @var InvoiceService
     */
    private $invoiceService;
    /**
     * @var ProjectService
     */
    private $projectService;
    /**
     * @var ContactService
     */
    private $contactService;
    /**
     * @var AffiliationService
     */
    private $affiliationService;
    /**
     * @var DoaService
     */
    private $doaService;
    /**
     * @var LoiService
     */
    private $loiService;
    /**
     * @var GeneralService
     */
    private $generalService;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var FormService
     */
    private $formService;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        OrganisationService $organisationService,
        InvoiceService $invoiceService,
        ProjectService $projectService,
        ContactService $contactService,
        AffiliationService $affiliationService,
        DoaService $doaService,
        LoiService $loiService,
        GeneralService $generalService,
        EntityManager $entityManager,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->organisationService = $organisationService;
        $this->invoiceService = $invoiceService;
        $this->projectService = $projectService;
        $this->contactService = $contactService;
        $this->affiliationService = $affiliationService;
        $this->doaService = $doaService;
        $this->loiService = $loiService;
        $this->generalService = $generalService;
        $this->entityManager = $entityManager;
        $this->formService = $formService;
        $this->translator = $translator;
    }


    public function listAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $organisationQuery = $this->organisationService
            ->findFiltered(Organisation::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new OrganisationFilter($this->organisationService);

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'           => $paginator,
                'form'                => $form,
                'encodedFilter'       => urlencode($filterPlugin->getHash()),
                'organisationService' => $this->organisationService,
                'order'               => $filterPlugin->getOrder(),
                'direction'           => $filterPlugin->getDirection(),
            ]
        );
    }

    public function listDuplicateAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $organisationQuery = $this->organisationService
            ->findDuplicateOrganisations($filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new OrganisationFilter($this->organisationService);

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'           => $paginator,
                'form'                => $form,
                'encodedFilter'       => urlencode($filterPlugin->getHash()),
                'organisationService' => $this->organisationService,
                'order'               => $filterPlugin->getOrder(),
                'direction'           => $filterPlugin->getDirection(),
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getInvoiceFilter();

        $invoiceQuery = $this->invoiceService->findFiltered(
            Invoice::class,
            array_merge($filterPlugin->getFilter(), ['organisation' => [$organisation->getId()]])
        );

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($invoiceQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $invoiceFilter = new InvoiceFilter($this->invoiceService);
        $invoiceFilter->setData(['filter' => $filterPlugin->getFilter()]);

        $mergeForm = new OrganisationMerge($this->entityManager, $organisation);

        return new ViewModel(
            [
                'paginator'           => $paginator,
                'invoiceFilter'       => $invoiceFilter,
                'encodedFilter'       => urlencode($filterPlugin->getHash()),
                'order'               => $filterPlugin->getOrder(),
                'direction'           => $filterPlugin->getDirection(),
                'organisation'        => $organisation,
                'organisationService' => $this->organisationService,
                'organisationDoa'     => $this->doaService->findDoaByOrganisation($organisation),
                'organisationLoi'     => $this->loiService->findLoiByOrganisation($organisation),
                'projectService'      => $this->projectService,
                'affiliations'        => $this->affiliationService->findAffiliationByOrganisation($organisation),
                'mergeForm'           => $mergeForm,
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction()
    {
        $organisation = new Organisation();
        /** @var Request $request */
        $request = $this->getRequest();
        $data = array_merge($request->getPost()->toArray(), $request->getFiles()->toArray());
        $form = $this->formService->prepare($organisation, $data);
        $form->remove('delete');

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/list');
            }

            if ($form->isValid()) {
                /** @var Organisation $organisation */
                $organisation = $form->getData();
                $organisation->getDescription()->setOrganisation($organisation);
                // Ignore empty description
                if (empty($organisation->getDescription()->getDescription())) {
                    $organisation->setDescription(null);
                }

                $fileData = $this->params()->fromFiles();

                if (!empty($fileData['file']['name'])) {
                    $logo = new Logo();
                    $logo->setOrganisation($organisation);
                    $logo->setOrganisationLogo(file_get_contents($fileData['file']['tmp_name']));
                    $imageSizeValidator = new ImageSize();
                    $imageSizeValidator->isValid($fileData['file']);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['file']);
                    $logo->setContentType(
                        $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );
                    $logo->setLogoExtension($logo->getContentType()->getExtension());
                    $organisation->getLogo()->add($logo);
                }

                $this->organisationService->save($organisation);
                $this->flashMessenger()->setNamespace('success')->addMessage(
                    sprintf(
                        $this->translator->translate('txt-organisation-%s-has-successfully-been-added'),
                        $organisation
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/organisation/view', ['id' => $organisation->getId()]);
            }
        }

        return new ViewModel(
            [
                'form'         => $form,
                'organisation' => $organisation
            ]
        );
    }

    public function editAction()
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($organisation, $data);

        if (!$this->organisationService->canDeleteOrganisation($organisation)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/view', ['id' => $organisation->getId()]);
            }

            if (isset($data['delete']) && $this->organisationService->canDeleteOrganisation($organisation)) {
                $this->flashMessenger()->setNamespace('success')->addMessage(
                    sprintf(
                        $this->translator->translate('txt-organisation-%s-has-been-removed-successfully'),
                        $organisation
                    )
                );

                $this->organisationService->delete($organisation);

                return $this->redirect()->toRoute('zfcadmin/organisation/list');
            }

            if ($form->isValid()) {
                /** @var Organisation $organisation */
                $organisation = $form->getData();
                $organisation->getDescription()->setOrganisation($organisation);
                // Remove an empty description
                if (empty($organisation->getDescription()->getDescription())) {
                    $this->organisationService->delete($organisation->getDescription());
                    $organisation->setDescription(null);
                }

                $fileData = $this->params()->fromFiles();

                if (!empty($fileData['file']['name'])) {
                    $logo = $organisation->getLogo()->first();
                    if (!$logo) {
                        // Create a new logo element
                        $logo = new Logo();
                        $logo->setOrganisation($organisation);
                    }
                    $logo->setOrganisationLogo(file_get_contents($fileData['file']['tmp_name']));
                    $imageSizeValidator = new ImageSize();
                    $imageSizeValidator->isValid($fileData['file']);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['file']);
                    $logo->setContentType(
                        $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );
                    $logo->setLogoExtension($logo->getContentType()->getExtension());
                    $organisation->getLogo()->add($logo);
                }

                $this->organisationService->save($organisation);

                $this->flashMessenger()->setNamespace('success')->addMessage(
                    sprintf(
                        $this->translator->translate('txt-organisation-%s-has-successfully-been-updated'),
                        $organisation
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/organisation/view', ['id' => $organisation->getId()]);
            }
        }

        return new ViewModel(
            [
                'organisation' => $organisation,
                'form'         => $form,
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function manageWebAction()
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }


        $form = new ManageWeb($organisation);
        //Prepare an array for population
        $population = [];
        foreach ($organisation->getWeb() as $web) {
            $population['webFieldset'][$web->getId()] = ['delete' => ''];

            /** @var Fieldset $webFieldset */
            $webFieldset = $form->get('webFieldset');

            //inject the existing webs in the array
            foreach ($webFieldset as $webId => $webElement) {
                if ($webId === $web->getId()) {
                    $webElement->get('web')->setValue($web->getWeb());
                    $webElement->get('main')->setValue((int)$web->getMain());
                }
            }
        }

        $data = ArrayUtils::merge($population, $this->getRequest()->getPost()->toArray(), true);


        $form->setInputFilter(new \Organisation\InputFilter\ManageWeb($organisation));
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/view', ['id' => $this->params('id')]);
            }

            if ($form->isValid()) {
                $data = $form->getData();

                if (isset($data['webFieldset']) && is_array($data['webFieldset'])) {
                    foreach ($data['webFieldset'] as $webId => $information) {
                        /**
                         * //Find the corresponding web
                         *
                         * @var $web Web
                         */
                        $web = $this->organisationService->find(Web::class, (int)$webId);

                        if (isset($information['delete']) && $information['delete'] === '1') {
                            $this->organisationService->delete($web);
                        } else {
                            $web->setOrganisation($organisation);
                            $web->setWeb($information['web']);
                            $web->setMain((int)$information['main']);
                            $this->organisationService->save($web);
                        }
                    }
                }

                //Handle the new web (if provided)
                if (!empty($data['web'])) {
                    $web = new Web();
                    $web->setOrganisation($organisation);
                    $web->setWeb($data['web']);
                    $web->setMain((int)$data['main']);

                    $this->organisationService->save($web);
                }

                if (isset($data['submit'])) {
                    return $this->redirect()->toRoute('zfcadmin/organisation/view', ['id' => $this->params('id')]);
                }

                return $this->redirect()->toRoute('zfcadmin/organisation/manage-web', ['id' => $this->params('id')]);
            }
        }

        return new ViewModel(
            [
                'organisationService' => $this->organisationService,
                'organisation'        => $organisation,
                'form'                => $form,
            ]
        );
    }

    public function addAffiliationAction()
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = new AddAffiliation($this->projectService, $organisation);
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $organisation->getId()],
                    ['fragment' => 'project']
                );
            }

            if ($form->isValid()) {
                $formData = $form->getData();

                $project = $this->projectService->findProjectById((int)$formData['project']);
                $contact = $this->contactService->findContactById((int)$formData['contact']);
                $branch = $formData['branch'];

                $affiliation = new Affiliation();
                $affiliation->setProject($project);
                $affiliation->setOrganisation($organisation);
                if (!empty($branch)) {
                    $affiliation->setBranch($branch);
                }
                $affiliation->setContact($contact);

                $this->affiliationService->save($affiliation);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate(
                            'txt-organisation-%s-has-successfully-been-added-to-project-%s'
                        ),
                        $organisation,
                        $project
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $organisation->getId()],
                    ['fragment' => 'project']
                );
            }
        }


        return new ViewModel(
            [
                'organisation' => $organisation,
                'form'         => $form,
            ]
        );
    }

    public function mergeAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var Organisation $source */
        $source = $this->organisationService->findOrganisationById((int)$this->params('sourceId'));
        /** @var Organisation $target */
        $target = $this->organisationService->findOrganisationById((int)$this->params('targetId'));

        if (null === $source || null === $target) {
            return $this->notFoundAction();
        }

        if ($request->isPost()) {
            $data = $request->getPost()->toArray();

            // Cancel the merge
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $target->getId()],
                    ['fragment' => 'merge']
                );
            }

            // Swap source and destination
            if (isset($data['swap'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/merge',
                    ['sourceId' => $target->getId(), 'targetId' => $source->getId()]
                );
            }

            // Do the merge
            if (isset($data['merge'])) {
                $result = $this->mergeOrganisation()->merge($source, $target);
                $tab = 'general';
                if ($result['success']) {
                    $this->flashMessenger()->setNamespace('success')->addMessage(
                        $this->translator->translate('txt-organisations-have-been-successfully-merged')
                    );
                } else {
                    $tab = 'merge';
                    $this->flashMessenger()->setNamespace('error')->addMessage(
                        $this->translator->translate('txt-organisation-merge-failed')
                    );
                }

                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $target->getId()],
                    ['fragment' => $tab]
                );
            }
        }

        return new ViewModel(
            [
                'errors'              => $this->mergeOrganisation()->checkMerge($source, $target),
                'source'              => $source,
                'target'              => $target,
                'mergeForm'           => new OrganisationMerge(),
                'organisationService' => $this->organisationService,
            ]
        );
    }
}
