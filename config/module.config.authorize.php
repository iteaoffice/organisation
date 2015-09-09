<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */
namespace Organisation;

use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        /* Currently, only controller and route guards exist
         */
        'guards'             => [
            /* If this guard is specified here (i.e. it is enabled], it will block
             * access to all routes unless they are specified here.
             */
            Route::class => [
                ['route' => 'assets/organisation-logo', 'roles' => []],
                ['route' => 'organisation/logo', 'roles' => []],
                ['route' => 'organisation/search', 'roles' => []],
                ['route' => 'organisation/json/get-branches', 'roles' => []],
                ['route' => 'organisation-manager/list', 'roles' => ['office']],
                ['route' => 'organisation-manager/edit', 'roles' => ['office']],
                ['route' => 'organisation-manager/new', 'roles' => ['office']],
                ['route' => 'organisation-manager/view', 'roles' => ['office']],
            ],
        ],
    ],
];
