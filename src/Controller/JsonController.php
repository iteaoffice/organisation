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

use DragonBe\Vies\Vies;
use Organisation\Entity\Financial;
use Zend\View\Model\JsonModel;

/**
 * Class JsonController
 * @package Organisation\Controller
 */
class JsonController extends OrganisationAbstractController
{
    /**
     *
     */
    public function getBranchesAction(): JsonModel
    {
        $organisationId = (int)$this->getEvent()->getRequest()->getPost()->get('organisationId');
        $organisation = $this->getOrganisationService()->findOrganisationById($organisationId);

        if (\is_null($organisation)) {
            return new JsonModel();
        }

        $options = $this->getOrganisationService()->findBranchesByOrganisation($organisation);
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

    /**
     * @return JsonModel
     */
    public function checkVatAction(): JsonModel
    {
        $financialId = (int)$this->getRequest()->getPost('financialId');
        /**
         * @var $financial Financial
         */
        $financial = $this->getOrganisationService()->findEntityById(Financial::class, $financialId);

        if (\is_null($financial->getVat()) && \is_null($this->getRequest()->getPost('vat'))) {
            return new JsonModel(['success' => 'error', 'result' => $this->translate("txt-vat-number-empty")]);
        }

        $vat = $financial->getVat();

        //Overrule the vat when a VAT number is sent via the URL
        if (!\is_null($this->getRequest()->getPost('vat'))) {
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
                $financial->setDateVat(new \DateTime());
                $this->getOrganisationService()->updateEntity($financial);


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
            $financial->setDateVat(new \DateTime());
            $this->getOrganisationService()->updateEntity($financial);

            return new JsonModel(
                [
                    'success' => 'error',
                    'result'  => 'Invalid',
                    'status'  => Financial::VAT_STATUS_INVALID,
                ]
            );
        } catch (\Throwable $e) {
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
