<?php

/**
 * Jield copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
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
