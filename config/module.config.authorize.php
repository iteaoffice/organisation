<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2015 ITEA Office (https://itea3.org]
 */
namespace Organisation;

use Admin\Entity\Access;
use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        /* Currently, only controller and route guards exist
         */
        'guards' => [
            /* If this guard is specified here (i.e. it is enabled], it will block
             * access to all routes unless they are specified here.
             */
            Route::class => [
                ['route' => 'assets/organisation-logo', 'roles' => []],
                ['route' => 'organisation/logo', 'roles' => []],
                ['route' => 'organisation/search', 'roles' => []],
                ['route' => 'organisation/json/get-branches', 'roles' => []],
                ['route' => 'organisation/json/check-vat', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/list', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/add-affiliation', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation-type/list', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation-type/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation-type/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation-type/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/search-form', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/financial/list', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/financial/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/financial/no-financial', 'roles' => [Access::ACCESS_OFFICE]],
            ],
        ],
    ],
];
