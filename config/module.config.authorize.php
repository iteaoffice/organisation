<?php

/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation;

use BjyAuthorize\Guard\Route;
use Organisation\Acl\Assertion\UpdateAssertion;

return [
    'bjyauthorize' => [
        /* Currently, only controller and route guards exist
         */
        'guards' => [
            /* If this guard is specified here (i.e. it is enabled], it will block
             * access to all routes unless they are specified here.
             */
            Route::class => [
                ['route' => 'image/organisation-logo', 'roles' => []],
                ['route' => 'image/organisation-update-logo', 'roles' => []],
                ['route' => 'organisation/json/get-branches', 'roles' => []],
                ['route' => 'organisation/json/check-vat', 'roles' => ['office']],
                ['route' => 'organisation/json/search', 'roles' => ['office']],
                ['route' => 'organisation/json/search-parent', 'roles' => ['office']],
                [
                    'route'     => 'community/organisation/update',
                    'roles'     => [],
                    'assertion' => UpdateAssertion::class,
                ],
                ['route' => 'zfcadmin/organisation/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/list-duplicate', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/list-inactive', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/manage-web', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/merge', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/add-affiliation', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation-type/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation-type/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation-type/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation-type/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/search-form', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/financial/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/financial/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/financial/no-financial', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/note/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/note/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/selection/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/selection/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/selection/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/selection/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/selection/copy', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/selection/export', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/selection/edit-sql', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/update/pending', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/update/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/update/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/organisation/update/approve', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/list-no-member', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/list-no-member-export', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/import/project', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/financial/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/financial/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/financial/no-financial', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/add-organisation', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/overview-variable-contribution', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/overview-variable-contribution-pdf', 'roles' => ['office']],
                [
                    'route' => 'zfcadmin/parent/overview-extra-variable-contribution',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/parent/overview-extra-variable-contribution-pdf',
                    'roles' => ['office'],
                ],
                ['route' => 'zfcadmin/parent/add-organisation', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/organisation/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/organisation/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/organisation/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/organisation/add-affiliation', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/organisation/merge', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/organisation/affiliation/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/organisation/affiliation/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/organisation/affiliation/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent-type/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent-type/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent-type/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent-type/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent-status/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent-status/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent-status/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent-status/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/doa/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/doa/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/doa/download', 'roles' => ['office']],
                ['route' => 'zfcadmin/parent/doa/upload', 'roles' => ['office']],
                ['route' => 'cli-organisation-cleanup', 'roles' => [],],
            ],
        ],
    ],
];
