<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
return [
    'navigation' => [
        'community' => [],
        'admin'     => [
            // And finally, here is where we define our page hierarchy
            'organisation' => [
                'label'    => _("txt-organisation-admin"),
                'route'    => 'zfcadmin/organisation-manager/list',
                'resource' => 'zfcadmin',
                'pages'    => [
                    'organisations' => [
                        'label' => "txt-organisations",
                        'route' => 'zfcadmin/organisation-manager/list',
                    ],
                ],
            ],
        ],
    ],
];
