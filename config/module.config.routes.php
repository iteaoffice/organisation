<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Project
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
use Organisation\Controller;

return [
    'router' => [
        'routes' => [
            'assets'       => [
                'type'          => 'Literal',
                'options'       => [
                    'route'    => '/assets/' . (defined("DEBRANOVA_HOST") ? DEBRANOVA_HOST : 'test'),
                    'defaults' => [
                        'controller' => Controller\OrganisationController::class,
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
                                'controller' => Controller\OrganisationController::class,
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
                        'controller' => Controller\OrganisationController::class,
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
                                'controller' => Controller\JsonController::class,
                                'action'     => 'get-branch',
                            ]
                        ],
                        'child_routes' => [
                            'get-branches' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/get-branches.json',
                                    'defaults' => [
                                        'controller' => Controller\JsonController::class,
                                        'action'     => 'get-branches',
                                    ],
                                    'query'    => [
                                        'organisationId' => null
                                    ]
                                ]
                            ],
                            'check-vat'    => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/check-vat[/vat-:vat].json',
                                    'defaults' => [
                                        'controller' => Controller\JsonController::class,
                                        'action'     => 'check-vat',
                                    ],
                                    'query'    => [
                                        'financialId' => null
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
                    'organisation' => [
                        'type'          => 'Segment',
                        'priority'      => 1000,
                        'options'       => [
                            'route'    => '/organisation',
                            'defaults' => [
                                'controller' => Controller\OrganisationAdminController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'list'            => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'new'             => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'view'            => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/view/[:id][/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'edit'            => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'add-affiliation' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/add-affiliation/[:id].html',
                                    'defaults' => [
                                        'action' => 'add-affiliation',
                                    ],
                                ],
                            ],
                            'search-form'     => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/search-form.html',
                                    'defaults' => [
                                        'action' => 'search-form',
                                    ],
                                ],
                            ],
                            'financial'       => [
                                'type'          => 'Segment',
                                'priority'      => 1000,
                                'options'       => [
                                    'route'    => '/vat',
                                    'defaults' => [
                                        'controller' => Controller\OrganisationFinancialController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes'  => [
                                    'list' => [
                                        'type'     => 'Segment',
                                        'priority' => 1000,
                                        'options'  => [
                                            'route'    => '/list[/:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                    ],

                                    'check'        => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/check/[:id].html',
                                            'defaults' => [
                                                'action' => 'check',
                                            ],
                                        ],
                                    ],
                                    'edit'         => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                    'no-financial' => [
                                        'type'     => 'Segment',
                                        'priority' => 1000,
                                        'options'  => [
                                            'route'    => '/no-financial.html',
                                            'defaults' => [
                                                'action' => 'no-financial',
                                            ],
                                        ],
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
