<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\Organisation;

use Doctrine\Common\Collections\ArrayCollection;
use General\Entity\VatType;
use General\Service\GeneralService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Entity\Financial;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;

/**
 * Class OrganisationFinancialController
 *
 * @package Organisation\Controller
 */
final class FinancialController extends AbstractController
{
    private OrganisationService $organisationService;
    private FormService $formService;
    private GeneralService $generalService;
    private TranslatorInterface $translator;

    public function __construct(
        OrganisationService $organisationService,
        FormService $formService,
        GeneralService $generalService,
        TranslatorInterface $translator
    ) {
        $this->organisationService = $organisationService;
        $this->formService         = $formService;
        $this->generalService      = $generalService;
        $this->translator          = $translator;
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
                    'zfcadmin/organisation/details/financial',
                    ['id' => $organisation->getId()],
                );
            }

            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/details/financial',
                    ['id' => $organisation->getId()],
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
                    $vatType         = $this->generalService->find(VatType::class, (int)$data['vatType']);
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
                    'zfcadmin/organisation/details/financial',
                    ['id' => $organisation->getId()],
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
}
