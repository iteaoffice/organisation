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

use DateTime;
use DragonBe\Vies\Vies;
use Organisation\Entity\Financial;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;
use Throwable;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use function sprintf;
use function trim;

/**
 * Class JsonController
 *
 * @package Organisation\Controller
 */
final class JsonController extends OrganisationAbstractController
{
    private OrganisationService $organisationService;
    private ParentService $parentService;
    private TranslatorInterface $translator;

    public function __construct(
        OrganisationService $organisationService,
        ParentService $parentService,
        TranslatorInterface $translator
    ) {
        $this->organisationService = $organisationService;
        $this->parentService = $parentService;
        $this->translator = $translator;
    }

    public function getBranchesAction(): JsonModel
    {
        $organisationId = (int)$this->getRequest()->getPost('organisationId');
        $organisation = $this->organisationService->findOrganisationById($organisationId);

        if (null === $organisation) {
            return new JsonModel();
        }

        $options = $this->organisationService->findBranchesByOrganisation($organisation);
        asort($options);

        $branches = [];
        foreach ($options as $key => $branch) {
            $branchValue = [];
            $branchValue['value'] = $key;
            $branchValue['label'] = $branch;
            $branches[] = $branchValue;
        }

        return new JsonModel($branches);
    }

    public function searchAction(): ViewModel
    {
        $search = $this->getRequest()->getPost()->get('q');
        $results = [];
        foreach ($this->organisationService->searchOrganisation($search, 1000) as $result) {
            $text = trim(sprintf('%s (%s)', $result['organisation'], $result['iso3']));
            $results[] = ['value' => $result['id'], 'text' => $text,];
        }
        return new JsonModel($results);
    }

    public function searchParentAction(): ViewModel
    {
        $search = $this->getRequest()->getPost()->get('q');
        $results = [];
        foreach ($this->parentService->searchParent($search, 1000) as $result) {
            $text = trim(sprintf('%s (%s)', $result['organisation'], $result['iso3']));
            $results[] = ['value' => $result['id'], 'text' => $text,];
        }
        return new JsonModel($results);
    }

    public function checkVatAction(): JsonModel
    {
        $financialId = (int)$this->getRequest()->getPost('financialId');
        /**
         * @var $financial Financial
         */
        $financial = $this->organisationService->find(Financial::class, $financialId);

        if (null === $financial->getVat() && null === $this->getRequest()->getPost('vat')) {
            return new JsonModel(
                ['success' => 'error', 'result' => $this->translator->translate("txt-vat-number-empty")]
            );
        }

        $vat = $financial->getVat();

        //Overrule the vat when a VAT number is sent via the URL
        if (null !== $this->getRequest()->getPost('vat')) {
            $vat = $this->getRequest()->getPost('vat');
        }

        $vies = new Vies();
        if (!$vies->getHeartBeat()->isAlive()) {
            return new JsonModel(
                [
                    'success' => 'error',
                    'result'  => 'Service is not available at the moment, please try again later.',
                ]
            );
        }

        try {
            $result = $vies->validateVat(
                $financial->getOrganisation()->getCountry()->getCd(),
                trim(str_replace($financial->getOrganisation()->getCountry()->getCd(), '', $vat))
            );

            if ($result->isValid()) {
                //Update the financial
                $financial->setVatStatus(Financial::VAT_STATUS_VALID);
                $financial->setDateVat(new DateTime());
                $this->organisationService->save($financial);


                return new JsonModel(
                    [
                        'success' => 'success',
                        'result'  => 'Valid',
                        'status'  => Financial::VAT_STATUS_VALID,
                    ]
                );
            }

            //Update the financial
            $financial->setVatStatus(Financial::VAT_STATUS_INVALID);
            $financial->setDateVat(new DateTime());
            $this->organisationService->save($financial);

            return new JsonModel(
                [
                    'success' => 'error',
                    'result'  => 'Invalid',
                    'status'  => Financial::VAT_STATUS_INVALID,
                ]
            );
        } catch (Throwable $e) {
            return new JsonModel(
                [
                    'success' => 'error',
                    'result'  => $e->getMessage(),
                    'status'  => Financial::VAT_STATUS_UNDEFINED,
                ]
            );
        }
    }
}
