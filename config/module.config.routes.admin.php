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
                    'organisation' => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/organisation',
                            'defaults' => [
                                'controller' => Controller\Organisation\ManagerController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'new'                => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'edit'               => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'web'                => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/web/[:id].html',
                                    'defaults' => [
                                        'action' => 'web',
                                    ],
                                ],
                            ],
                            'create-affiliation' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/create-affiliation/[:id].html',
                                    'defaults' => [
                                        'action' => 'create-affiliation',
                                    ],
                                ],
                            ],
                            'merge'              => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/merge/[:sourceId]/into/[:targetId].html',
                                    'defaults' => [
                                        'action' => 'merge',
                                    ],
                                ],
                            ],

                            'details' => [
                                'type'          => 'Literal',
                                'options'       => [
                                    'route'    => '/details',
                                    'defaults' => [
                                        'controller' => Controller\Organisation\DetailsController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'general'   => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/general.html',
                                            'defaults' => [
                                                'action' => 'general',
                                            ],
                                        ],
                                    ],
                                    'parent'    => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/parent.html',
                                            'defaults' => [
                                                'action' => 'parent',
                                            ],
                                        ],
                                    ],
                                    'legal'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/legal.html',
                                            'defaults' => [
                                                'action' => 'legal',
                                            ],
                                        ],
                                    ],
                                    'financial' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/financial.html',
                                            'defaults' => [
                                                'action' => 'financial',
                                            ],
                                        ],
                                    ],
                                    'invoices'  => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/invoices.html',
                                            'defaults' => [
                                                'action' => 'invoices',
                                            ],
                                        ],
                                    ],
                                    'notes'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/notes.html',
                                            'defaults' => [
                                                'action' => 'notes',
                                            ],
                                        ],
                                    ],
                                    'contacts'  => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/contacts.html',
                                            'defaults' => [
                                                'action' => 'contact',
                                            ],
                                        ],
                                    ],
                                    'projects'  => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/projects.html',
                                            'defaults' => [
                                                'action' => 'project',
                                            ],
                                        ],
                                    ],
                                    'ideas'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/ideas.html',
                                            'defaults' => [
                                                'action' => 'idea',
                                            ],
                                        ],
                                    ],
                                    'merge'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/[:id]/merge.html',
                                            'defaults' => [
                                                'action' => 'merge',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'list'    => [
                                'type'          => 'Literal',
                                'options'       => [
                                    'route'    => '/list',
                                    'defaults' => [
                                        'controller' => Controller\Organisation\ListController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'organisation' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/parent[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'parent',
                                            ],
                                        ],
                                    ],
                                    'duplicate'    => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/duplicate[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'duplicate',
                                            ],
                                        ],
                                    ],
                                    'inactive'     => [
                                        'type'    => 'Literal',
                                        'options' => [
                                            'route'    => '/inactive.html',
                                            'defaults' => [
                                                'action' => 'inactive',
                                            ],
                                        ],
                                    ],
                                    'financial'    => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/financial[/:page].html',
                                            'defaults' => [
                                                'action' => 'financial',
                                            ],
                                        ],
                                    ],
                                    'no-financial' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/no-financial.html',
                                            'defaults' => [
                                                'action' => 'no-financial',
                                            ],
                                        ],
                                    ],
                                ],
                            ],


                            'financial' => [
                                'type'          => 'Segment',
                                'priority'      => 1000,
                                'options'       => [
                                    'route'    => '/financial',
                                    'defaults' => [
                                        'controller' => Controller\Organisation\FinancialController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes'  => [
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
                            'note'      => [
                                'type'          => 'Segment',
                                'options'       => [
                                    'route'    => '/note',
                                    'defaults' => [
                                        'controller' => Controller\Organisation\NoteController::class,
                                        'action'     => 'edit',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'new'  => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/new/organisation-[:organisationId].html',
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

                            'update'    => [
                                'type'          => 'Segment',
                                'options'       => [
                                    'route'    => '/update',
                                    'defaults' => [
                                        'controller' => Controller\Update\ManagerController::class,
                                        'action'     => 'list',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'pending' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/pending.html',
                                            'defaults' => [
                                                'action' => 'pending',
                                            ],
                                        ],
                                    ],
                                    'edit'    => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                    'view'    => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                    'approve' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/approve/[:id].html',
                                            'defaults' => [
                                                'action' => 'approve',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'type'      => [
                                'type'          => 'Segment',
                                'options'       => [
                                    'route'    => '/type',
                                    'defaults' => [
                                        'controller' => Controller\Organisation\TypeController::class,
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
                            'selection' => [
                                'type'          => 'Segment',
                                'options'       => [
                                    'route'    => '/selection',
                                    'defaults' => [
                                        'controller' => Controller\SelectionController::class,
                                        'action'     => 'list',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'list'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                    ],
                                    'view'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                    'new'      => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/new.html',
                                            'defaults' => [
                                                'action' => 'new',
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
                                    'copy'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/copy/[:id].html',
                                            'defaults' => [
                                                'action' => 'copy',
                                            ],
                                        ],
                                    ],
                                    'edit-sql' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/edit/sql/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit-sql',
                                            ],
                                        ],
                                    ],
                                    'export'   => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/export/[:type]/[:id].html',
                                            'defaults' => [
                                                'action' => 'export',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'board'        => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/board',
                            'defaults' => [
                                'controller' => Controller\BoardController::class,
                                'action'     => 'list',
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
                                    'route'    => '/new[/organisation-:organisationId].html',
                                    'defaults' => [
                                        'action' => 'new',
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
                ],
            ],
        ],
    ],
];
