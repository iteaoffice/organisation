<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Organisation;

return [
    'router' => [
        'routes' => [
            'zfcadmin' => [
                'child_routes' => [
                    'parent' => [
                        'type'         => 'Segment',
                        'options'      => [
                            'route'    => '/parent',
                            'defaults' => [
                                'controller' => Controller\Parent\ManagerController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'child_routes' => [
                            'list'             => [
                                'type'          => 'Literal',
                                'options'       => [
                                    'route'    => '/list',
                                    'defaults' => [
                                        'action'     => 'list',
                                        'controller' => Controller\Parent\ListController::class,
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'parent'           => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/parent[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'parent',
                                            ],
                                        ],
                                    ],
                                    'no-member'        => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/no-member-doa[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'no-member',
                                            ],
                                        ],
                                    ],
                                    'no-member-export' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/no-member-doa/export[/f-:encodedFilter].csv',
                                            'defaults' => [
                                                'action' => 'no-member-export',
                                            ],
                                        ],
                                    ],
                                    'no-financial'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/no-financial.html',
                                            'defaults' => [
                                                'action' => 'no-financial',
                                            ],
                                        ],
                                    ],
                                ]
                            ],
                            'financial'        => [
                                'type'         => 'Literal',
                                'options'      => [
                                    'route'    => '/financial',
                                    'defaults' => [
                                        'action'     => 'not-found',
                                        'controller' => Controller\Parent\FinancialController::class
                                    ],
                                ],
                                'child_routes' => [
                                    'new'  => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/new/parent-[:parentId].html',
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

                                ],
                            ],
                            'new'              => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/new[/organisation-:organisationId].html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'edit'             => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'add-organisation' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/add-organisation/[:id][/organisation-:organisationId].html',
                                    'defaults' => [
                                        'action' => 'add-organisation',
                                    ],
                                ],
                            ],
                            'import'           => [
                                'type'         => 'Literal',
                                'options'      => [
                                    'route'    => '/import',
                                    'defaults' => [
                                        'controller' => Controller\Parent\ImportController::class,
                                        'action'     => 'import',
                                    ],
                                ],
                                'child_routes' => [
                                    'project' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/project.html',
                                            'defaults' => [
                                                'action' => 'import-project',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'details'          => [
                                'type'          => 'Literal',
                                'options'       => [
                                    'route'    => '/details',
                                    'defaults' => [
                                        'controller' => Controller\Parent\DetailsController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'general'       => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/general.html',
                                            'defaults' => [
                                                'action' => 'general',
                                            ],
                                        ],
                                    ],
                                    'organisations' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/organisations.html',
                                            'defaults' => [
                                                'action' => 'organisations',
                                            ],
                                        ],
                                    ],
                                    'doas'          => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/doas.html',
                                            'defaults' => [
                                                'action' => 'doas',
                                            ],
                                        ],
                                    ],
                                    'financial'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/financial.html',
                                            'defaults' => [
                                                'action' => 'financial',
                                            ],
                                        ],
                                    ],
                                    'invoices'      => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/invoices.html',
                                            'defaults' => [
                                                'action' => 'invoices',
                                            ],
                                        ],
                                    ],
                                ]
                            ],

                            'contribution' => [
                                'type'          => 'Literal',
                                'options'       => [
                                    'route'    => '/contribution',
                                    'defaults' => [
                                        'controller' => Controller\Parent\ContributionController::class,
                                        'action'     => 'view',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'variable'           => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/variable/[:id]/year-[:year]/program-[:program].html',
                                            'defaults' => [
                                                'action' => 'variable',
                                            ],
                                        ],
                                    ],
                                    'variable-pdf'       => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/variable/[:id]/year-[:year]/program-[:program].pdf',
                                            'defaults' => [
                                                'action' => 'variable-pdf',
                                            ],
                                        ],
                                    ],
                                    'extra-variable'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/extra-variable/[:id]/year-[:year]/program-[:program].html',
                                            'defaults' => [
                                                'action' => 'extra-variable',
                                            ],
                                        ],
                                    ],
                                    'extra-variable-pdf' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/extra-variable/[:id]/year-[:year]/program-[:program].pdf',
                                            'defaults' => [
                                                'action' => 'extra-variable-pdf',
                                            ],
                                        ],
                                    ],
                                ]
                            ],

                            'organisation' => [
                                'type'          => 'Literal',
                                'options'       => [
                                    'route'    => '/organisation',
                                    'defaults' => [
                                        'controller' => Controller\Parent\OrganisationController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'list'            => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                    ],
                                    'view'            => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/view/[:id].html',
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
                                    'merge'           => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/merge/[:id].html',
                                            'defaults' => [
                                                'action' => 'merge',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'doa'          => [
                                'type'          => 'Literal',
                                'options'       => [
                                    'route'    => '/doa',
                                    'defaults' => [
                                        'controller' => Controller\Parent\DoaController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'view'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                    'download' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/download/[:id].html',
                                            'defaults' => [
                                                'action' => 'download',
                                            ],
                                        ],
                                    ],
                                    'edit'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                    'upload'   => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/upload/parent-[:parentId].html',
                                            'defaults' => [
                                                'action' => 'upload',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'type'         => [
                                'type'          => 'Segment',
                                'options'       => [
                                    'route'    => '/type',
                                    'defaults' => [
                                        'controller' => Controller\Parent\TypeController::class,
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
                        ],
                    ],
                ],
            ],
        ],
    ],
];
