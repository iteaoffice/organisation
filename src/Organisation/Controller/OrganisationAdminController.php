<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Organisation\Controller;

use Affiliation\Service\AffiliationServiceAwareInterface;
use Affiliation\Service\DoaServiceAwareInterface;
use Affiliation\Service\LoiServiceAwareInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Invoice\Form\InvoiceFilter;
use Invoice\Service\InvoiceServiceAwareInterface;
use Organisation\Entity\Organisation;
use Organisation\Form\OrganisationFilter;
use Project\Service\ProjectService;
use Project\Service\ProjectServiceAwareInterface;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * @category    Organisation
 *
 */
class OrganisationAdminController extends OrganisationAbstractController implements
    DoaServiceAwareInterface,
    LoiServiceAwareInterface,
    AffiliationServiceAwareInterface,
    InvoiceServiceAwareInterface,
    ProjectServiceAwareInterface
{
    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $organisationQuery = $this->getOrganisationService()->findEntitiesFiltered(
            'organisation',
            $filterPlugin->getFilter()
        );

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
    public function viewAction()
    {
        $organisationService = $this->getOrganisationService()->setOrganisationId($this->params('id'));

        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getInvoiceFilter();

        $invoiceQuery = $this->getInvoiceService()->findEntitiesFiltered(
            'invoice',
            array_merge(
                $filterPlugin->getFilter(),
                [
                    'organisation' => [$organisationService->getOrganisation()->getId()],
                ]
            )
        );

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($invoiceQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 15);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        $form = new InvoiceFilter($this->getInvoiceService());
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        $projects = $this->getProjectService()->findProjectByOrganisation(
            $organisationService->getOrganisation(),
            ProjectService::WHICH_ALL
        );

        return new ViewModel([
            'paginator'           => $paginator,
            'form'                => $form,
            'encodedFilter'       => urlencode($filterPlugin->getHash()),
            'order'               => $filterPlugin->getOrder(),
            'direction'           => $filterPlugin->getDirection(),
            'organisationService' => $organisationService,
            'organisationDoa'     => $this->getDoaService()->findDoaByOrganisation($organisationService->getOrganisation()),
            'organisationLoi'     => $this->getLoiService()->findLoiByOrganisation($organisationService->getOrganisation()),
            'projects'            => $projects,

        ]);
    }

    /**
     * @return ViewModel
     */
    public function editAction()
    {
        $organisationService = $this->getOrganisationService()->setOrganisationId($this->params('id'));

        $data = array_merge(
            $this->getRequest()->getPost()->toArray()
        );
        $form = $this->getFormService()->prepare(
            $organisationService->getOrganisation(),
            $organisationService->getOrganisation(),
            $data
        );

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $organisationService->getOrganisation()->getId()]
                );
            }

            if ($form->isValid()) {
                /**
                 * @var $organisation Organisation
                 */
                $organisation = $form->getData();
                $this->getOrganisationService()->updateEntity($organisation);

                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $organisationService->getOrganisation()->getId()]
                );
            }
        }

        return new ViewModel(
            [
                'organisationService' => $organisationService,
                'form'                => $form,
            ]
        );
    }

    /**
     * @return JsonModel
     */
    public function searchFormAction()
    {
        $search = $this->getRequest()->getPost()->get('search');

        $results = [];
        foreach ($this->getOrganisationService()->searchOrganisation($search, 1000, null, false, false) as $result) {
            $text = trim(
                sprintf(
                    "%s (%s)",
                    $result['organisation'],
                    $result['iso3']
                )
            );

            $results[] = [
                'value' => $result['id'],
                'text'  => $text,
            ];
        }

        return new JsonModel($results);
    }
}
