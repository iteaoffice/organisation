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
            ->findEntitiesFiltered(Organisation::class, $filterPlugin->getFilter());

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
        $organisation = $this->getOrganisationService()->findOrganisationById($this->params('id'));

        if (is_null($organisation)) {
            return $this->notFoundAction();
        }

        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getInvoiceFilter();

        $invoiceQuery = $this->getInvoiceService()
            ->findEntitiesFiltered('invoice', array_merge($filterPlugin->getFilter(), [
                'organisation' => [
                    $organisation->getId()
                ],
            ]));

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($invoiceQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 15);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        $form = new InvoiceFilter($this->getInvoiceService());
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel([
            'paginator'           => $paginator,
            'form'                => $form,
            'encodedFilter'       => urlencode($filterPlugin->getHash()),
            'order'               => $filterPlugin->getOrder(),
            'direction'           => $filterPlugin->getDirection(),
            'organisation'        => $organisation,
            'organisationService' => $this->getOrganisationService(),
            'organisationDoa'     => $this->getDoaService()->findDoaByOrganisation($organisation),
            'organisationLoi'     => $this->getLoiService()->findLoiByOrganisation($organisation),
            'projectService'      => $this->getProjectService()

        ]);
    }

    /**
     * @return ViewModel
     */
    public function editAction()
    {
        $organisation = $this->getOrganisationService()->findOrganisationById($this->params('id'));

        if (is_null($organisation)) {
            return $this->notFoundAction();
        }


        $data = array_merge([
            'description' => $organisation->getDescription()
        ], $this->getRequest()->getPost()->toArray());
        $form = $this->getFormService()->prepare($organisation, $organisation, $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/view', ['id' => $organisation->getId()]);
            }

            if ($form->isValid()) {
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        $this->translate("txt-organisation-%s-has-successfully-been-updated"),
                        $organisation
                    ));
                /**
                 * @var $organisation Organisation
                 */
                $organisation = $form->getData();


                $fileData = $this->params()->fromFiles();
                if (!empty($fileData['file']['name'])) {
                    $logo = $organisation->getLogo()->first();
                    if (!$logo) {
                        //Create a logo element
                        $logo = new Logo();
                        $logo->setOrganisation($organisation);
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

                return $this->redirect()->toRoute('zfcadmin/organisation/view', ['id' => $organisation->getId()]);
            }
        }

        return new ViewModel([
            'organisation' => $organisation,
            'form'         => $form,
        ]);
    }


    /**
     * @return ViewModel
     */
    public function addAffiliationAction()
    {
        /** @var Organisation $organisation */
        $organisation = $this->getOrganisationService()->findOrganisationById($this->params('id'));

        if (is_null($organisation)) {
            return $this->notFoundAction();
        }

        $data = array_merge($this->getRequest()->getPost()->toArray());

        $form = new AddAffiliation($this->getProjectService(), $organisation);
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

                $project = $this->getProjectService()->findProjectById((int)$formData['project']);
                $contact = $this->getContactService()->findContactById((int)$formData['contact']);
                $branch = $formData['branch'];

                $affiliation = new Affiliation();
                $affiliation->setProject($project);
                $affiliation->setOrganisation($organisation);
                if (!empty($branch)) {
                    $affiliation->setBranch($branch);
                }
                $affiliation->setContact($contact);

                $this->getAffiliationService()->newEntity($affiliation);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        $this->translate("txt-organisation-%s-has-successfully-been-added-to-project-%s"),
                        $organisation,
                        $project
                    ));

                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $organisation->getId()],
                    ['fragment' => 'project']
                );
            }
        }


        return new ViewModel([
            'organisation' => $organisation,
            'form'         => $form,
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
