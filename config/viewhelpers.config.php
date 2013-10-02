<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Application
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */

return array(
    'factories'  => array(
        'organisationHandler' => function ($sm) {
            return new \Organisation\View\Helper\OrganisationHandler($sm);
        },
    ),
    'invokables' => array(
        'organisationLogo' => 'Organisation\View\Helper\OrganisationLogo',
        'organisationLink' => 'Organisation\View\Helper\OrganisationLink',
    )
);
