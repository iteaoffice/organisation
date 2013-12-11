<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
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
