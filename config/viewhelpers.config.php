<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

use Organisation\View\Helper;

return array(
    'factories'  => array(
        'organisationHandler'      => function ($sm) {
                return new Helper\OrganisationHandler($sm);
            },
        'organisationServiceProxy' => function ($sm) {
                return new Helper\OrganisationServiceProxy($sm);
            },
        'organisationLink'         => function ($sm) {
                return new Helper\OrganisationLink($sm);
            },
    ),
    'invokables' => array(
        'organisationLogo' => 'Organisation\View\Helper\OrganisationLogo',
    )
);
