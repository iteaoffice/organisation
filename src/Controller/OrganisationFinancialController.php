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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use General\Entity\VatType;
use General\Service\GeneralService;
use Organisation\Entity\Financial;
use Organisation\Form;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * Class OrganisationFinancialController
 *
 * @package Organisation\Controller
 */
final class OrganisationFinancialController extends OrganisationAbstractController
{
    /**
     * @var OrganisationService
     */
    private $organisationService;
    /**
     * @var FormService
     */
    private $formService;
    /**
     * @var GeneralService
     */
    private $generalService;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        OrganisationService $organisationService,
        FormService $formService,
        GeneralService $generalService,
        TranslatorInterface $translator
    ) {
        $this->organisationService = $organisationService;
        $this->formService = $formService;
        $this->generalService = $generalService;
        $this->translator = $translator;
    }


    public function listAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $organisationQuery = $this->organisationService->findOrganisationFinancialList($filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\OrganisationFilter($this->organisationService);

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

    public function editAction()
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        if (null === ($financial = $organisation->getFinancial())) {
            $financial = new Financial();
            $financial->setOrganisation($organisation);
        }

        $data = array_merge(
            [
                'vatType' => $financial->getVatType()->count() === 0 ? 0 : $financial->getVatType()->first()->getId(),
            ],
            $this->getRequest()->getPost()->toArray()
        );

        $form = $this->formService->prepare($financial, $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['delete'])) {
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate(
                            'txt-financial-organisation-of-%s-has-successfully-been-removed'
                        ),
                        $organisation
                    )
                );

                $this->organisationService->delete($financial);

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

                if ($data['vatType'] === '0') {
                    $financial->setVatType(new ArrayCollection());
                } else {
                    $vatType = $this->generalService->find(VatType::class, (int)$data['vatType']);
                    $arrayCollection = new ArrayCollection();
                    $arrayCollection->add($vatType);
                    $financial->setVatType($arrayCollection);
                }


                $this->organisationService->save($financial);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-financial-organisation-%s-has-successfully-been-updated'),
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
                'organisationService' => $this->organisationService,
                'organisation'        => $organisation,
                'financial'           => $financial,
                'form'                => $form,
            ]
        );
    }

    public function noFinancialAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $organisationQuery = $this->organisationService
            ->findActiveOrganisationWithoutFinancial($filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\OrganisationFilter($this->organisationService);

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'           => $paginator,
                'form'                => $form,
                'encodedFilter'       => urlencode($filterPlugin->getHash()),
                'order'               => $filterPlugin->getOrder(),
                'direction'           => $filterPlugin->getDirection(),
                'organisationService' => $this->organisationService,
            ]
        );
    }
}
