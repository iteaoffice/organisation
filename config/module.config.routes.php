<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

use Organisation\Controller;

return [
    'router' => [
        'routes' => [
            'image'        => [
                'child_routes' => [
                    'organisation-logo'        => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/o/[:id]-[:last-update].[:ext]',
                            'defaults' => [
                                //Explicitly add the controller here as the assets are collected
                                'controller' => Controller\ImageController::class,
                                'action'     => 'organisation-logo',
                            ],
                        ],
                    ],
                    'organisation-update-logo' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/u/[:id]-[:last-update].[:ext]',
                            'defaults' => [
                                // Explicitly add the controller here as the assets are collected
                                'controller' => Controller\ImageController::class,
                                'action'     => 'organisation-update-logo',
                            ],
                        ],
                    ],
                ],
            ],
            'organisation' => [
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => [
                    'route' => '/organisation',
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'json' => [
                        'type'         => 'Segment',
                        'options'      => [
                            'route'    => '/json',
                            'defaults' => [
                                'controller' => Controller\JsonController::class,
                                'action'     => 'get-branch',
                            ],
                        ],
                        'child_routes' => [
                            'get-branches'  => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/get-branches.json',
                                    'defaults' => [
                                        'controller' => Controller\JsonController::class,
                                        'action'     => 'get-branches',
                                    ],
                                    'query'    => [
                                        'organisationId' => null,
                                    ],
                                ],
                            ],
                            'check-vat'     => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/check-vat[/vat-:vat].json',
                                    'defaults' => [
                                        'controller' => Controller\JsonController::class,
                                        'action'     => 'check-vat',
                                    ],
                                    'query'    => [
                                        'financialId' => null,
                                    ],
                                ],
                            ],
                            'search'        => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/search.json',
                                    'defaults' => [
                                        'controller' => Controller\JsonController::class,
                                        'action'     => 'search',
                                    ],
                                ],
                            ],
                            'search-parent' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/search-parent.json',
                                    'defaults' => [
                                        'controller' => Controller\JsonController::class,
                                        'action'     => 'search-parent',
                                    ],
                                ],
                            ],
                        ],

                    ],
                    'logo' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => '/logo/[:id].[:ext]',
                            'constraints' => [
                                'id' => '\d+',
                            ],
                            'defaults'    => [
                                'action' => 'logo',
                            ],
                        ],
                    ],
                ],
            ],
            'community'    => [
                'child_routes' => [
                    'organisation' => [
                        'type'          => 'Segment',
                        'priority'      => 1001,
                        'options'       => [
                            'route' => '/organisation',
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'update' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/update/[:organisationId].html',
                                    'constraints' => [
                                        'organisationId' => '\d+',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\UpdateController::class,
                                        'action'     => 'new',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
