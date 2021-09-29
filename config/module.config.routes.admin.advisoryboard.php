<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Organisation;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'zfcadmin' => [
                'child_routes' => [
                    'advisory-board' => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route' => '/advisory-board',
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'city' => [
                                'type'          => Literal::class,
                                'options'       => [
                                    'route'    => '/city',
                                    'defaults' => [
                                        'controller' => Controller\AdvisoryBoard\City\ManagerController::class,
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'list'    => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                    ],
                                    'new'     => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/new.html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'edit'    => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                    'details' => [
                                        'type'          => Literal::class,
                                        'options'       => [
                                            'route'    => '/details',
                                            'defaults' => [
                                                'controller' => Controller\AdvisoryBoard\City\Manager\DetailsController::class,
                                            ],
                                        ],
                                        'may_terminate' => false,
                                        'child_routes'  => [
                                            'general' => [
                                                'type'    => Segment::class,
                                                'options' => [
                                                    'route'    => '/[:id]/general.html',
                                                    'defaults' => [
                                                        'action' => 'general',
                                                    ],
                                                ],
                                            ],
                                        ]
                                    ],
                                ]
                            ],
                            'tender' => [
                                'type'          => Literal::class,
                                'options'       => [
                                    'route'    => '/tender',
                                    'defaults' => [
                                        'controller' => Controller\AdvisoryBoard\Tender\ManagerController::class,
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'list'    => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                    ],
                                    'new'     => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/new.html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'edit'    => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                    'details' => [
                                        'type'          => Literal::class,
                                        'options'       => [
                                            'route'    => '/details',
                                            'defaults' => [
                                                'controller' => Controller\AdvisoryBoard\Tender\Manager\DetailsController::class,
                                            ],
                                        ],
                                        'may_terminate' => false,
                                        'child_routes'  => [
                                            'general' => [
                                                'type'    => Segment::class,
                                                'options' => [
                                                    'route'    => '/[:id]/general.html',
                                                    'defaults' => [
                                                        'action' => 'general',
                                                    ],
                                                ],
                                            ],
                                        ]
                                    ],
                                    'type'      => [
                                        'type'          => 'Segment',
                                        'options'       => [
                                            'route'    => '/type',
                                            'defaults' => [
                                                'controller' => Controller\AdvisoryBoard\Tender\TypeController::class,
                                                'action'     => 'index',
                                            ],
                                        ],
                                        'may_terminate' => false,
                                        'child_routes'  => [
                                            'list' => [
                                                'type'    => 'Segment',
                                                'options' => [
                                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                                    'defaults' => [
                                                        'action' => 'list',
                                                    ],
                                                ],
                                            ],
                                            'new'  => [
                                                'type'    => 'Segment',
                                                'options' => [
                                                    'route'    => '/new.html',
                                                    'defaults' => [
                                                        'action' => 'new',
                                                    ],
                                                ],
                                            ],
                                            'edit' => [
                                                'type'    => 'Segment',
                                                'options' => [
                                                    'route'    => '/edit/[:id].html',
                                                    'defaults' => [
                                                        'action' => 'edit',
                                                    ],
                                                ],
                                            ],
                                            'view' => [
                                                'type'    => 'Segment',
                                                'options' => [
                                                    'route'    => '/view/[:id].html',
                                                    'defaults' => [
                                                        'action' => 'view',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ]
                            ],
                            'solution' => [
                                'type'          => Literal::class,
                                'options'       => [
                                    'route'    => '/solution',
                                    'defaults' => [
                                        'controller' => Controller\AdvisoryBoard\Solution\ManagerController::class,
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'list'    => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                    ],
                                    'new'     => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/new.html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'edit'    => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                    'details' => [
                                        'type'          => Literal::class,
                                        'options'       => [
                                            'route'    => '/details',
                                            'defaults' => [
                                                'controller' => Controller\AdvisoryBoard\Solution\Manager\DetailsController::class,
                                            ],
                                        ],
                                        'may_terminate' => false,
                                        'child_routes'  => [
                                            'general' => [
                                                'type'    => Segment::class,
                                                'options' => [
                                                    'route'    => '/[:id]/general.html',
                                                    'defaults' => [
                                                        'action' => 'general',
                                                    ],
                                                ],
                                            ],
                                        ]
                                    ],
                                ]
                            ],
                        ]
                    ],
                ],
            ],
        ],
    ],
];
