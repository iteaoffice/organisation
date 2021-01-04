<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

use Organisation\Controller\ConsoleController;

return [
    'console' => [
        'router' => [
            'routes' => [
                'cli-organisation-cleanup' => [
                    'options' => [
                        'route'    => 'organisation cleanup',
                        'defaults' => [
                            'controller' => ConsoleController::class,
                            'action'     => 'organisation-cleanup',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
