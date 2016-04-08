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

use DragonBe\Vies\Vies;
use Organisation\Entity\Financial;
use Zend\View\Model\JsonModel;

/**
 *
 */
class JsonController extends OrganisationAbstractController
{
    /**
     *
     */
    public function getBranchesAction()
    {
        $organisationId = (int)$this->getEvent()->getRequest()->getPost()->get('organisationId');
        $organisation = $this->getOrganisationService()->findOrganisationById($organisationId);

        if (is_null($organisation)) {
            return $this->notFoundAction();
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
    public function checkVatAction()
    {
        $financialId = (int)$this->getEvent()->getRequest()->getPost()->get('financialId');
        /**
         * @var $financial Financial
         */
        $financial = $this->getOrganisationService()->findEntityById(Financial::class, $financialId);

        if (is_null($financial->getVat()) && is_null($this->getEvent()->getRequest()->getPost()->get('vat'))) {
            return new JsonModel(['success' => 'error', 'result' => $this->translate("txt-vat-number-empty")]);
        }

        //Overrule the vat when a VAT number is sent via the URL
        if (!is_null($this->getEvent()->getRequest()->getPost()->get('vat'))) {
            $vat = $this->getEvent()->getRequest()->getPost()->get('vat');
        } else {
            $vat = $financial->getVat();
        }

        $vies = new Vies();
        if (false === $vies->getHeartBeat()->isAlive()) {
            return new JsonModel([
                'success' => 'error',
                'result'  => 'Service is not available at the moment, please try again later.'
            ]);
        } else {
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


                    return new JsonModel([
                        'success' => 'success',
                        'result'  => 'Valid',
                        'status'  => Financial::VAT_STATUS_VALID
                    ]);
                } else {
                    //Update the financial
                    $financial->setVatStatus(Financial::VAT_STATUS_INVALID);
                    $financial->setDateVat(new \DateTime());
                    $this->getOrganisationService()->updateEntity($financial);

                    return new JsonModel([
                        'success' => 'error',
                        'result'  => 'Invalid',
                        'status'  => Financial::VAT_STATUS_INVALID
                    ]);
                }
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => 'error',
                    'result'  => $e->getMessage(),
                    'status'  => Financial::VAT_STATUS_UNDEFINED
                ]);
            }
        }
    }
}
