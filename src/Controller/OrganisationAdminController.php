<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
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
use Exception;
use General\Service\GeneralService;
use Invoice\Search\Service\InvoiceSearchService;
use Invoice\Service\InvoiceService;
use Organisation\Entity\Logo;
use Organisation\Entity\Organisation;
use Organisation\Entity\Web;
use Organisation\Form\AddAffiliation;
use Organisation\Form\ManageWeb;
use Organisation\Form\OrganisationFilter;
use Organisation\Form\OrganisationMerge;
use Organisation\Search\Service\OrganisationSearchService;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;
use Search\Form\SearchResult;
use Search\Paginator\Adapter\SolariumPaginator;
use Solarium\QueryType\Select\Query\Query as SolariumQuery;
use Laminas\Form\Fieldset;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\File\ImageSize;
use Laminas\Validator\File\MimeType;
use Laminas\View\Model\ViewModel;

use function http_build_query;
use function implode;
use function sprintf;

/**
 * Class OrganisationAdminController
 *
 * @package Organisation\Controller
 */
class OrganisationAdminController extends OrganisationAbstractController
{
    private OrganisationService $organisationService;
    private OrganisationSearchService $searchService;
    private InvoiceService $invoiceService;
    private InvoiceSearchService $invoiceSearchService;
    private ProjectService $projectService;
    private ContactService $contactService;
    private AffiliationService $affiliationService;
    private DoaService $doaService;
    private LoiService $loiService;
    private GeneralService $generalService;
    private EntityManager $entityManager;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(
        OrganisationService $organisationService,
        OrganisationSearchService $organisationSearchService,
        InvoiceService $invoiceService,
        InvoiceSearchService $invoiceSearchService,
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
        $this->searchService = $organisationSearchService;
        $this->invoiceService = $invoiceService;
        $this->invoiceSearchService = $invoiceSearchService;
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
        /** @var Request $request */
        $request = $this->getRequest();
        $page = $this->params('page', 1);
        $form = new SearchResult();
        $data = array_merge(
            [
                'order'     => '',
                'direction' => '',
                'query'     => '',
                'facet'     => [],
            ],
            $request->getQuery()->toArray()
        );
        $searchFields = [
            'organisation_search', //To search for numbers
            'description_search',
            'organisation_type_search',
            'country_search',
        ];

        if ($request->isGet()) {
            $this->searchService->setSearch($data['query'], $searchFields, $data['order'], $data['direction']);
            if (isset($data['facet'])) {
                foreach ($data['facet'] as $facetField => $values) {
                    $quotedValues = [];
                    foreach ($values as $value) {
                        $quotedValues[] = sprintf('"%s"', $value);
                    }

                    $this->searchService->addFilterQuery(
                        $facetField,
                        implode(' ' . SolariumQuery::QUERY_OPERATOR_OR . ' ', $quotedValues)
                    );
                }
            }

            $form->addSearchResults(
                $this->searchService->getQuery()->getFacetSet(),
                $this->searchService->getResultSet()->getFacetSet()
            );
            $form->setData($data);
        }

        $paginator = new Paginator(
            new SolariumPaginator($this->searchService->getSolrClient(), $this->searchService->getQuery())
        );
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? 1000 : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        return new ViewModel(
            [
                'form'      => $form,
                'order'     => $data['order'],
                'direction' => $data['direction'],
                'query'     => $data['query'],
                'badges'    => $form->getBadges(),
                'arguments' => http_build_query($form->getFilteredData()),
                'paginator' => $paginator,
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

    public function listInactiveAction(): ViewModel
    {
        $inactiveOrganisations = $this->organisationService->findInactiveOrganisations();

        return new ViewModel(
            [
                'inactiveOrganisations' => $inactiveOrganisations
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        /** @var Request $request */
        $request = $this->getRequest();
        $page = $this->params('page', 1);
        $form = new SearchResult();
        $data = array_merge(
            [
                'order'     => '',
                'direction' => '',
                'query'     => '',
                'facet'     => [],
            ],
            $request->getQuery()->toArray()
        );

        $searchFields = [
            'invoice_number_search',
            'organisation_search',
            'contact_search',
            'reference_search',
            'type_search',
            'vat_type_search',
            'status_name_search',
            'status_explanation_search',
            'day_book_number_search',
        ];

        if ($request->isGet()) {
            $this->invoiceSearchService->setSearchByOrganisation(
                $organisation,
                $data['query'],
                $searchFields,
                $data['order'],
                $data['direction']
            );
            if (isset($data['facet'])) {
                foreach ($data['facet'] as $facetField => $values) {
                    $quotedValues = [];
                    foreach ($values as $value) {
                        $quotedValues[] = (string) $value;
                    }

                    $this->invoiceSearchService->addFilterQuery(
                        $facetField,
                        implode(' ' . SolariumQuery::QUERY_OPERATOR_OR . ' ', $quotedValues)
                    );
                }
            }

            $form->addSearchResults(
                $this->invoiceSearchService->getQuery()->getFacetSet(),
                $this->invoiceSearchService->getResultSet()->getFacetSet()
            );
            $form->setData($data);
        }

        $paginator = new Paginator(
            new SolariumPaginator($this->invoiceSearchService->getSolrClient(), $this->invoiceSearchService->getQuery())
        );
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? 1000 : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $mergeForm = new OrganisationMerge($this->entityManager, $organisation);

        return new ViewModel(
            [
                'form'                => $form,
                'order'               => $data['order'],
                'direction'           => $data['direction'],
                'query'               => $data['query'],
                'badges'              => $form->getBadges(),
                'arguments'           => http_build_query($form->getFilteredData()),
                'paginator'           => $paginator,
                'organisation'        => $organisation,
                'invoiceService'      => $this->invoiceService,
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
     * @return Response|ViewModel
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

                if (! empty($fileData['file']['name'])) {
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
                $this->flashMessenger()->addSuccessMessage(
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

        if (! $this->organisationService->canDeleteOrganisation($organisation)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/view', ['id' => $organisation->getId()]);
            }

            if (isset($data['delete']) && $this->organisationService->canDeleteOrganisation($organisation)) {
                $this->flashMessenger()->addSuccessMessage(
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

                if (! empty($fileData['file']['name'])) {
                    $logo = $organisation->getLogo()->first();
                    if (! $logo) {
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
                    $logo->setLogoExtension((string) $logo->getContentType()->getExtension());
                    $organisation->getLogo()->add($logo);
                }

                $this->organisationService->save($organisation);

                $this->flashMessenger()->addSuccessMessage(
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
     * @return Response|ViewModel
     * @throws Exception
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
                if (! empty($data['web'])) {
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
                if (! empty($branch)) {
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
                    $this->flashMessenger()->addSuccessMessage(
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
