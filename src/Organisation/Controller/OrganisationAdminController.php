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

use Affiliation\Entity\Affiliation;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Invoice\Form\InvoiceFilter;
use Organisation\Entity\Logo;
use Organisation\Entity\Organisation;
use Organisation\Form\AddAffiliation;
use Organisation\Form\OrganisationFilter;
use Project\Service\ProjectService;
use Zend\Paginator\Paginator;
use Zend\Validator\File\ImageSize;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * @category    Organisation
 *
 */
class OrganisationAdminController extends OrganisationAbstractController
{
    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $organisationQuery = $this->getOrganisationService()
            ->findEntitiesFiltered('organisation', $filterPlugin->getFilter());

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


    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $organisationService = $this->getOrganisationService()->setOrganisationId($this->params('id'));

        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getInvoiceFilter();

        $invoiceQuery = $this->getInvoiceService()
            ->findEntitiesFiltered('invoice', array_merge($filterPlugin->getFilter(), [
                'organisation' => [
                    $organisationService->getOrganisation()->getId()
                ],
            ]));

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($invoiceQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 15);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        $form = new InvoiceFilter($this->getInvoiceService());
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        $projects = $this->getProjectService()
            ->findProjectByOrganisation($organisationService->getOrganisation(), ProjectService::WHICH_ALL);

        return new ViewModel([
            'paginator'           => $paginator,
            'form'                => $form,
            'encodedFilter'       => urlencode($filterPlugin->getHash()),
            'order'               => $filterPlugin->getOrder(),
            'direction'           => $filterPlugin->getDirection(),
            'organisationService' => $organisationService,
            'organisationDoa'     => $this->getDoaService()
                ->findDoaByOrganisation($organisationService->getOrganisation()),
            'organisationLoi'     => $this->getLoiService()
                ->findLoiByOrganisation($organisationService->getOrganisation()),
            'projects'            => $projects,
            'projectService'      => $this->getProjectService()

        ]);
    }

    /**
     * @return ViewModel
     */
    public function editAction()
    {
        $organisationService = $this->getOrganisationService()->setOrganisationId($this->params('id'));

        $data = array_merge([
            'description' => $organisationService->getOrganisation()->getDescription()
        ], $this->getRequest()->getPost()->toArray());
        $form = $this->getFormService()
            ->prepare($organisationService->getOrganisation(), $organisationService->getOrganisation(), $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute('zfcadmin/organisation/view', ['id' => $organisationService->getOrganisation()->getId()]);
            }

            if ($form->isValid()) {
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        $this->translate("txt-organisation-%s-has-successfully-been-updated"),
                        $organisationService->getOrganisation()
                    ));
                /**
                 * @var $organisation Organisation
                 */
                $organisation = $form->getData();


                $fileData = $this->params()->fromFiles();
                if (!empty($fileData['file']['name'])) {
                    $logo = $organisationService->getOrganisation()->getLogo()->first();
                    if (!$logo) {
                        //Create a logo element
                        $logo = new Logo();
                        $logo->setOrganisation($organisationService->getOrganisation());
                    }
                    $logo->setOrganisationLogo(file_get_contents($fileData['file']['tmp_name']));
                    $imageSizeValidator = new ImageSize();
                    $imageSizeValidator->isValid($fileData['file']);
                    $logo->setContentType($this->getGeneralService()
                        ->findContentTypeByContentTypeName($fileData['file']['type']));
                    $logo->setLogoExtension($logo->getContentType()->getExtension());

                    $organisation->getLogo()->add($logo);

                    /**
                     * Remove the cached file
                     */
                    if (file_exists($logo->getCacheFileName())) {
                        unlink($logo->getCacheFileName());
                    }
                }

                $this->getOrganisationService()->updateEntity($organisation);

                return $this->redirect()
                    ->toRoute('zfcadmin/organisation/view', ['id' => $organisationService->getOrganisation()->getId()]);
            }
        }

        return new ViewModel([
            'organisationService' => $organisationService,
            'form'                => $form,
        ]);
    }


    /**
     * @return ViewModel
     */
    public function addAffiliationAction()
    {
        $organisationService = $this->getOrganisationService()->setOrganisationId($this->params('id'));

        $data = array_merge($this->getRequest()->getPost()->toArray());

        $form = new AddAffiliation($this->getOrganisationService(), $this->getProjectService());
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute(
                        'zfcadmin/organisation/view',
                        ['id' => $organisationService->getOrganisation()->getId()],
                        ['fragment' => 'project']
                    );
            }

            if ($form->isValid()) {
                $formData = $form->getData();

                $project = $this->getProjectService()->setProjectId((int)$formData['project'])->getProject();
                $contact = $this->getContactService()->findEntityById('contact', (int)$formData['contact']);
                $branch = $formData['branch'];

                $affiliation = new Affiliation();
                $affiliation->setProject($project);
                $affiliation->setOrganisation($organisationService->getOrganisation());
                if (!empty($branch)) {
                    $affiliation->setBranch($branch);
                }
                $affiliation->setContact($contact);

                $this->getAffiliationService()->newEntity($affiliation);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        $this->translate("txt-organisation-%s-has-successfully-been-added-to-project-%s"),
                        $organisationService->getOrganisation(),
                        $project
                    ));

                return $this->redirect()
                    ->toRoute(
                        'zfcadmin/organisation/view',
                        ['id' => $organisationService->getOrganisation()->getId()],
                        ['fragment' => 'project']
                    );
            }
        }


        return new ViewModel([
            'organisationService' => $organisationService,
            'form'                => $form,
        ]);
    }


    /**
     * @return JsonModel
     */
    public function searchFormAction()
    {
        $search = $this->getRequest()->getPost()->get('search');

        $results = [];
        foreach ($this->getOrganisationService()->searchOrganisation($search, 1000, null, false, false) as $result) {
            $text = trim(sprintf("%s (%s)", $result['organisation'], $result['iso3']));

            $results[] = ['value' => $result['id'], 'text' => $text,];
        }

        return new JsonModel($results);
    }
}
