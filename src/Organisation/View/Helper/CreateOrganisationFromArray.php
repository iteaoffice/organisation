<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\View\Helper;

use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;
use Zend\View\Helper\AbstractHelper;

/**
 * Class VersionServiceProxy.
 */
class CreateOrganisationFromArray extends AbstractHelper
{
    /**
     * @param array $organisationDetails
     *
     * @return OrganisationService
     */
    public function __invoke(array $organisationDetails)
    {
        $organisation = new Organisation();
        foreach ($organisationDetails as $key => $value) {
            $organisation->$key = $value;
        }

        return $organisation;
    }
}
