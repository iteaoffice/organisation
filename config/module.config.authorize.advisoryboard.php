<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation;

use BjyAuthorize\Guard\Route;

return [
    'bjyauthorize' => [
        'guards' => [
            Route::class => [
                ['route' => 'image/advisory-board/city-image', 'roles' => []],
                ['route' => 'image/advisory-board/solution-image', 'roles' => []],

                ['route' => 'zfcadmin/advisory-board/city/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/advisory-board/city/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/advisory-board/city/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/advisory-board/city/details/general', 'roles' => ['office']],


                ['route' => 'zfcadmin/advisory-board/tender/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/advisory-board/tender/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/advisory-board/tender/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/advisory-board/tender/details/general', 'roles' => ['office']],

                ['route' => 'zfcadmin/advisory-board/tender/type/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/advisory-board/tender/type/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/advisory-board/tender/type/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/advisory-board/tender/type/view', 'roles' => ['office']],


                ['route' => 'zfcadmin/advisory-board/solution/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/advisory-board/solution/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/advisory-board/solution/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/advisory-board/solution/details/general', 'roles' => ['office']],

            ],
        ],
    ],
];
