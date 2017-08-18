<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */
declare(strict_types=1);

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
                ['route' => 'zfcadmin/organisation/list-duplicate', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/merge', 'roles' => [Access::ACCESS_OFFICE]],
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
                ['route' => 'zfcadmin/organisation/note/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/organisation/note/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/list', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/list-no-member', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/import/parent', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/import/project', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/financial/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/financial/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/financial/no-financial', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/add-organisation', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/overview-variable-contribution', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/overview-variable-contribution-pdf', 'roles' => [Access::ACCESS_OFFICE]],
                [
                    'route' => 'zfcadmin/parent/overview-extra-variable-contribution',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/parent/overview-extra-variable-contribution-pdf',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                ['route' => 'zfcadmin/parent/add-organisation', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/organisation/list', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/organisation/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/organisation/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/organisation/add-affiliation', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/organisation/affiliation/list', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/organisation/affiliation/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/organisation/affiliation/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent-type/list', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent-type/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent-type/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent-type/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent-status/list', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent-status/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent-status/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent-status/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/doa/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/doa/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/doa/download', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/parent/doa/upload', 'roles' => [Access::ACCESS_OFFICE]],
            ],
        ],
    ],
];
