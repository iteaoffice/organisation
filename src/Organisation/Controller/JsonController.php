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

        $this->getOrganisationService()->setOrganisationId($organisationId);

        if ($this->getOrganisationService()->isEmpty()) {
            return $this->notFoundAction();
        }

        $options = $this->getOrganisationService()->findBranchesByOrganisation($this->getOrganisationService()->getOrganisation());
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
        $financial = $this->getOrganisationService()->findEntityById('financial', $financialId);

        if (is_null($financial->getVat())) {
            return new JsonModel(['success' => 'error', 'result' => $this->translate("txt-vat-number-empty")]);
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
                    trim(str_replace($financial->getOrganisation()->getCountry()->getCd(), '', $financial->getVat()))
                );

                if ($result->isValid()) {
                    //Update the financial
                    $financial->setVatStatus(Financial::VAT_STATUS_VALID);
                    $financial->setDateVat(new \DateTime());
                    $this->getOrganisationService()->updateEntity($financial);


                    return new JsonModel(['success' => 'success', 'result' => 'Valid']);
                } else {
                    //Update the financial
                    $financial->setVatStatus(Financial::VAT_STATUS_INVALID);
                    $financial->setDateVat(new \DateTime());
                    $this->getOrganisationService()->updateEntity($financial);

                    return new JsonModel(['success' => 'error', 'result' => 'Invalid']);
                }
            } catch (\Exception $e) {
                return new JsonModel(['success' => 'error', 'result' => $e->getMessage()]);
            }
        }
    }
}
