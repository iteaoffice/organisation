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

use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Invoice\Service\InvoiceService;
use Organisation\Entity;
use Organisation\Form;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;
use Program\Entity\Program;
use Program\Service\ProgramService;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Paginator\Paginator;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * @category    Parent
 */
final class ParentController extends OrganisationAbstractController
{
    /**
     * @var ParentService
     */
    private $parentService;
    /**
     * @var OrganisationService
     */
    private $organisationService;
    /**
     * @var ContactService
     */
    private $contactService;
    /**
     * @var ProgramService
     */
    private $programService;
    /**
     * @var InvoiceService
     */
    private $invoiceService;
    /**
     * @var FormService
     */
    private $formService;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ParentService $parentService,
        OrganisationService $organisationService,
        ContactService $contactService,
        ProgramService $programService,
        InvoiceService $invoiceService,
        FormService $formService,
        EntityManager $entityManager,
        TranslatorInterface $translator
    ) {
        $this->parentService = $parentService;
        $this->organisationService = $organisationService;
        $this->contactService = $contactService;
        $this->programService = $programService;
        $this->invoiceService = $invoiceService;
        $this->formService = $formService;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }


    public function listAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $query = $this->parentService->findFiltered(
            Entity\OParent::class,
            $filterPlugin->getFilter()
        );

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($query, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\ParentFilter($this->entityManager);

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

    public function listNoMemberAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $parentQuery = $this->parentService
            ->findActiveParentWhichAreNoMember($filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($parentQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\ParentFilter($this->entityManager);

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'           => $paginator,
                'form'                => $form,
                'encodedFilter'       => urlencode($filterPlugin->getHash()),
                'order'               => $filterPlugin->getOrder(),
                'direction'           => $filterPlugin->getDirection(),
                'organisationService' => $this->organisationService,
                'contactService'      => $this->contactService,
                'parentService'       => $this->parentService,
            ]
        );
    }

    public function listNoMemberExportAction()
    {
        $filterPlugin = $this->getOrganisationFilter();
        $parentQuery = $this->parentService
            ->findActiveParentWhichAreNoMember($filterPlugin->getFilter());

        /** @var Entity\OParent[] $parents */
        $parents = $parentQuery->getResult();

        // Open the output stream
        $fh = fopen('php://output', 'wb');


        ob_start();

        fputcsv(
            $fh,
            [
                'id',
                'name',
                'country',
                'iso3',
                'type',
                'member type',
                'artemisia type',
                'eposs type',
                'projects',
                'contact',
                'email',
                'street and number',
                'zip',
                'city',
                'country',
            ]
        );

        if (!empty($parents)) {
            foreach ($parents as $parent) {
                $projects = [];
                foreach ($parent->getParentOrganisation() as $parentOrganisation) {
                    foreach ($parentOrganisation->getAffiliation() as $affiliation) {
                        $projects[] = $affiliation->getProject()->parseFullName();
                    }
                }

                $address = $this->contactService->getMailAddress($parent->getContact());

                fputcsv(
                    $fh,
                    [
                        $parent->getId(),
                        $parent->getOrganisation()->getOrganisation(),
                        $parent->getOrganisation()->getCountry()->getCountry(),
                        $parent->getOrganisation()->getCountry()->getIso3(),
                        $parent->getType()->getType(),
                        $this->translator->translate($parent->getMemberType(true)),
                        $this->translator->translate($parent->getArtemisiaMemberType(true)),
                        $this->translator->translate($parent->getEpossMemberType(true)),
                        implode($projects, ';'),
                        $parent->getContact()->parseFullName(),
                        $parent->getContact()->getEmail(),
                        null !== $address ? $address->getAddress() : '',
                        null !== $address ? $address->getZipCode() : '',
                        null !== $address ? $address->getCity() : '',
                        null !== $address ? $address->getCountry()->getCountry() : '',
                    ]
                );
            }
        }

        $string = \ob_get_clean();

        // Convert to UTF-16LE
        $string = \mb_convert_encoding($string, 'UTF-16LE', 'UTF-8');

        // Prepend BOM
        $string = '\xFF\xFE' . $string;

        /** @var Response $response */
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'text/csv');
        $headers->addHeaderLine(
            'Content-Disposition',
            'attachment; filename=\'export-members-with-are-no-member-and-have-no-doa.csv\''
        );
        $headers->addHeaderLine('Accept-Ranges', 'bytes');
        $headers->addHeaderLine('Content-Length', \strlen($string));

        $response->setContent($string);

        return $response;
    }

    public function newAction()
    {
        $organisation = null;
        if (null !== $this->params('organisationId')) {
            $organisation = $this->organisationService->findOrganisationById((int)$this->params('organisationId'));
        }

        $data = $this->getRequest()->getPost()->toArray();

        $parent = new Entity\OParent();
        $form = $this->formService->prepare($parent, $data);
        $form->remove('delete');

        if (null !== $organisation) {
            //Inject the organisation in the form
            $form->get($parent->get('underscore_entity_name'))->get('organisation')
                ->setValueOptions([$organisation->getId() => $organisation->getOrganisation()]);

            $contactsInOrganisation = [];
            foreach ($this->contactService->findContactsInOrganisation($organisation) as $contact) {
                $contactsInOrganisation[$contact->getId()] = $contact->getFormName();
            }
            asort($contactsInOrganisation);

            //Inject the organisation in the form
            $form->get($parent->get('underscore_entity_name'))->get('contact')
                ->setValueOptions($contactsInOrganisation);
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/parent/list');
            }

            if ($form->isValid()) {
                /* @var $parent Entity\OParent */
                $parent = $form->getData();

                $parent->setDateParentTypeUpdate(new \DateTime());

                $this->parentService->save($parent);
                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    [
                        'id' => $parent->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function addOrganisationAction()
    {
        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $organisation = null;
        if (null !== $this->params('organisationId')) {
            $organisation = $this->organisationService->findOrganisationById((int)$this->params('organisationId'));
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = new Form\AddOrganisation();

        if (null !== $organisation) {
            //Inject the organisation in the form
            $form->get('organisation')->setValueOptions([$organisation->getId() => $organisation->getOrganisation()]);

            $contactsInOrganisation = [];
            foreach ($this->contactService->findContactsInOrganisation($organisation) as $contact) {
                $contactsInOrganisation[$contact->getId()] = $contact->getFormName();
            }
            asort($contactsInOrganisation);

            //Inject the organisation in the form
            $form->get('contact')->setValueOptions($contactsInOrganisation);
        }

        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
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
                $organisation = $this->organisationService->findOrganisationById((int)$data['organisation']);
                $parentOrganisation->setOrganisation($organisation);

                //Find the contact from the form.
                $contact = $this->contactService->findContactById((int)$data['contact']);
                $parentOrganisation->setContact($contact);


                $parentOrganisation = $this->parentService->save($parentOrganisation);
                return $this->redirect()->toRoute(
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

    public function editAction()
    {
        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $currentParentType = $parent->getType()->getId();

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($parent, $data);

        $form->get($parent->get('underscore_entity_name'))->get('contact')->injectContact($parent->getContact());
        $form->get($parent->get('underscore_entity_name'))->get('organisation')
            ->injectOrganisation($parent->getOrganisation());

        if (!$this->parentService->parentCanBeDeleted($parent)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/parent/list');
            }

            if (isset($data['delete']) && $this->parentService->parentCanBeDeleted($parent)) {
                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-parent-%s-has-successfully-been-deleted'), $parent)
                );

                $this->parentService->delete($parent);

                return $this->redirect()->toRoute('zfcadmin/parent/list');
            }

            if ($form->isValid()) {
                /* @var $parent Entity\OParent */
                $parent = $form->getData();

                if ($parent->getType()->getId() !== $currentParentType) {
                    $parent->setDateParentTypeUpdate(new \DateTime());
                }

                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-parent-%s-has-successfully-been-updated'), $parent)
                );

                $parent = $this->parentService->save($parent);

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    [
                        'id' => $parent->getId(),
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

    public function viewAction()
    {
        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $year = (int)date('Y');

        $form = new Form\CreateParentDoa($this->entityManager);
        $form->setData($this->getRequest()->getPost()->toArray());
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $counter = 0;
            foreach ((array)$form->getData()['program'] as $programId) {
                $program = $this->programService->findProgramById((int)$programId);
                if (null !== $program) {
                    $doa = new Entity\Parent\Doa();
                    $doa->setContact($parent->getContact());
                    $doa->setParent($parent);
                    $doa->setProgram($program);
                    $this->parentService->save($doa);
                    $counter++;
                }
            }

            $this->flashMessenger()->addSuccessMessage(
                sprintf(
                    $this->translator->translate('txt-%s-parent-doa-have-been-created-for-%s'),
                    $counter,
                    $parent
                )
            );

            return $this->redirect()->toRoute(
                'zfcadmin/parent/view',
                [
                    'id' => $parent->getId(),
                ],
                [
                    'fragment' => 'doa'
                ]
            );
        }

        return new ViewModel(
            [
                'parent'              => $parent,
                'organisationService' => $this->organisationService,
                'contactService'      => $this->contactService,
                'year'                => $year,
                'form'                => $form,
                'programs'            => $this->programService->findAll(Program::class),
                'parentService'       => $this->parentService
            ]
        );
    }

    public function overviewVariableContributionAction(): ViewModel
    {
        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $program = $this->programService->findProgramById((int)$this->params('program'));

        if (null === $program) {
            return $this->notFoundAction();
        }

        $year = (int)$this->params('year');

        $invoiceMethod = $this->invoiceService->findInvoiceMethod($program);

        return new ViewModel(
            [
                'year'          => $year,
                'parent'        => $parent,
                'program'       => $program,
                'invoiceMethod' => $invoiceMethod,
                'invoiceFactor' => $this->parentService->parseInvoiceFactor($parent, $program)

            ]
        );
    }

    public function overviewVariableContributionPdfAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        $program = $this->programService->findProgramById((int)$this->params('program'));

        if (null === $program) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        $year = (int)$this->params('year');

        $renderPaymentSheet = $this->renderOverviewVariableContributionSheet($parent, $program, $year);

        $response->getHeaders()->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine('Cache-Control: max-age=36000, must-revalidate')->addHeaderLine('Pragma: public')
            ->addHeaderLine(
                'Content-Disposition',
                'attachment; filename=\'' . sprintf(
                    'overview_variable_contribution_%s_%s.pdf',
                    $parent->getOrganisation()->getDocRef(),
                    $year
                ) . '\''
            )
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', \strlen($renderPaymentSheet->getPDFData()));
        $response->setContent($renderPaymentSheet->getPDFData());

        return $response;
    }

    public function overviewExtraVariableContributionAction(): ViewModel
    {
        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $program = $this->programService->findProgramById((int)$this->params('program'));

        if (null === $program) {
            return $this->notFoundAction();
        }

        $year = (int)$this->params('year');

        return new ViewModel(
            [
                'year'    => $year,
                'parent'  => $parent,
                'program' => $program

            ]
        );
    }

    public function overviewExtraVariableContributionPdfAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        $program = $this->programService->findProgramById((int)$this->params('program'));

        if (null === $program) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        $year = (int)$this->params('year');

        $renderPaymentSheet = $this->renderOverviewExtraVariableContributionSheet($parent, $program, $year);

        $response->getHeaders()->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine('Cache-Control: max-age=36000, must-revalidate')->addHeaderLine('Pragma: public')
            ->addHeaderLine(
                'Content-Disposition',
                'attachment; filename=\'' . sprintf(
                    'overview_extra_variable_contribution_%s_%s.pdf',
                    $parent->getOrganisation()->getDocRef(),
                    $year
                ) . '\''
            )
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', \strlen($renderPaymentSheet->getPDFData()));
        $response->setContent($renderPaymentSheet->getPDFData());

        return $response;
    }

    public function importProjectAction(): ViewModel
    {
        \set_time_limit(0);

        $data = \array_merge_recursive(
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
                $fileData = file_get_contents($data['file']['tmp_name']);

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
