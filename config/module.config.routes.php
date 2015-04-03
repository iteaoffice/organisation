<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Project
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
use Organisation\Controller\JsonController;

return [
    'router' => [
        'routes' => [
            'assets'       => [
                'type'          => 'Literal',
                'options'       => [
                    'route'    => '/assets/' . (defined("DEBRANOVA_HOST") ? DEBRANOVA_HOST : 'test'),
                    'defaults' => [
                        'controller' => 'organisation-index',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'organisation-logo' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => "/organisation-logo/[:id]-[:hash].[:ext]",
                            'defaults' => [
                                //Explicitly add the controller here as the assets are collected
                                'controller' => 'organisation-index',
                                'action'     => 'logo',
                            ],
                        ],
                    ],
                ],
            ],
            'organisation' => [
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => [
                    'route'    => '/organisation',
                    'defaults' => [
                        'controller' => 'organisation-index',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'json'   => [
                        'type'         => 'Segment',
                        'options'      => [
                            'route'    => '/json',
                            'defaults' => [
                                'controller' => JsonController::class,
                                'action'     => 'get-branch',
                            ]
                        ],
                        'child_routes' => [
                            'get-branches' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/get-branches.json',
                                    'defaults' => [
                                        'controller' => JsonController::class,
                                        'action'     => 'get-branches',
                                    ],
                                    'query'    => [
                                        'organisationId' => null
                                    ]
                                ]
                            ],
                        ]

                    ],
                    'search' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/search',
                            'defaults' => [
                                'action' => 'search',
                            ],
                        ],
                    ],
                    'logo'   => [
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
            'zfcadmin'     => [
                'child_routes' => [
                    'organisation-manager' => [
                        'type'          => 'Segment',
                        'priority'      => 1000,
                        'options'       => [
                            'route'    => '/organisation',
                            'defaults' => [
                                'controller' => 'organisation-manager',
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'list'   => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/list[/:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'new'    => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'view'   => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'edit'   => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/delete/[:id].html',
                                    'defaults' => [
                                        'action' => 'delete',
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
