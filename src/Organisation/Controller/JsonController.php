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
        $organisationId = (int) $this->getEvent()->getRequest()->getPost()->get('organisationId');

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
}
