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
    'navigation' => [
        'admin' => [
            // And finally, here is where we define our page hierarchy
            'organisation' => [
                'label' => _('txt-organisation-admin'),
                'order' => 30,
                'uri'   => '#',
                'pages' => [
                    'organisation' => [
                        'label' => _('txt-nav-organisation-list'),
                        'order' => 10,
                        'route' => 'zfcadmin/organisation/list/organisation',
                        'pages' => [
                            'organisation' => [
                                'route'   => 'zfcadmin/organisation/details/general',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Entity\Organisation::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\OrganisationLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'parent'             => [
                                        'label'   => _('txt-parent'),
                                        'route'   => 'zfcadmin/organisation/details/parent',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                        ],
                                    ],
                                    'legal'              => [
                                        'label'   => _('txt-legal'),
                                        'route'   => 'zfcadmin/organisation/details/legal',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                        ],
                                    ],
                                    'financial'          => [
                                        'label'   => _('txt-financial'),
                                        'route'   => 'zfcadmin/organisation/details/financial',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                        ],
                                        'pages'   => [
                                            'invoices' => [
                                                'label'   => _('txt-invoices'),
                                                'route'   => 'zfcadmin/organisation/details/invoices',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities' => [
                                                        'id' => Entity\Organisation::class,
                                                    ],
                                                ],
                                            ],
                                            'edit'     => [
                                                'label'   => _('txt-nav-edit-financial'),
                                                'route'   => 'zfcadmin/organisation/financial/edit',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities' => [
                                                        'id' => Entity\Organisation::class,
                                                    ],
                                                ],
                                            ],
                                        ]
                                    ],
                                    'notes'              => [
                                        'label'   => _('txt-notes'),
                                        'route'   => 'zfcadmin/organisation/details/notes',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                        ],
                                    ],
                                    'edit'               => [
                                        'label'   => _('txt-nav-edit'),
                                        'route'   => 'zfcadmin/organisation/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                        ],
                                    ],
                                    'web'                => [
                                        'label'   => _('txt-nav-manage-web'),
                                        'route'   => 'zfcadmin/organisation/web',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                        ],
                                    ],
                                    'create-affiliation' => [
                                        'label'   => _('txt-nav-add-affiliation'),
                                        'route'   => 'zfcadmin/organisation/create-affiliation',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                        ],
                                    ],


                                    'add-note'  => [
                                        'label'   => _('txt-nav-add-note'),
                                        'route'   => 'zfcadmin/organisation/note/new',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                            'routeParam' => [
                                                'id' => 'organisationId',
                                            ],
                                        ],
                                    ],
                                    'edit-note' => [
                                        'label'   => _('txt-nav-edit-note'),
                                        'route'   => 'zfcadmin/organisation/note/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Note::class,
                                            ],
                                        ],
                                    ],
                                    'merge'     => [
                                        'label'   => _('txt-nav-merge'),
                                        'route'   => 'zfcadmin/organisation/merge',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                            'routeParam' => [
                                                'id' => 'targetId',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new'          => [
                                'label'   => _('txt-add-organisation'),
                                'route'   => 'zfcadmin/organisation/new',
                                'visible' => false,
                            ],
                        ],
                    ],
                    'financial'    => [
                        'label' => _('txt-nav-financial-organisations'),
                        'order' => 30,
                        'route' => 'zfcadmin/organisation/list/financial',
                    ],
                    'board'        => [
                        'label' => _('txt-board-company-list'),
                        'route' => 'zfcadmin/board/list',
                        'order' => 35,
                        'pages' => [
                            'parent-type-view' => [
                                'route'   => 'zfcadmin/board/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Entity\Board::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\BoardLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'parent-type-edit' => [
                                        'label'   => _('txt-edit-board-company'),
                                        'route'   => 'zfcadmin/board/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Board::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'parent-new'       => [
                                'label' => _('txt-new-board-company'),
                                'route' => 'zfcadmin/board/new',
                            ],
                        ],
                    ],
                    'selection'    => [
                        'label' => _('txt-organisation-selection-list'),
                        'order' => 40,
                        'route' => 'zfcadmin/organisation/selection/list',
                        'pages' => [
                            'selection' => [
                                'route'   => 'zfcadmin/organisation/selection/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Entity\Selection::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\SelectionLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit'     => [
                                        'label'   => _('txt-organisation-selection-edit'),
                                        'route'   => 'zfcadmin/organisation/selection/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Selection::class,
                                            ],
                                        ],
                                    ],
                                    'copy'     => [
                                        'label'   => _('txt-organisation-selection-copy'),
                                        'route'   => 'zfcadmin/organisation/selection/copy',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Selection::class,
                                            ],
                                        ],
                                    ],
                                    'edit-sql' => [
                                        'label'   => _('txt-organisation-selection-edit-sql'),
                                        'route'   => 'zfcadmin/organisation/selection/edit-sql',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Selection::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new'       => [
                                'label' => _('txt-create-new-organisation-selection'),
                                'route' => 'zfcadmin/organisation/selection/new',
                            ],
                        ],
                    ],
                ],
            ],
            'config'       => [
                'pages' => [
                    'organisation-type-list' => [
                        'label' => _('txt-organisation-type-list'),
                        'route' => 'zfcadmin/organisation/type/list',
                        'pages' => [
                            'organisation-type-view' => [
                                'route'   => 'zfcadmin/organisation/type/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Entity\Type::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\TypeLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'organisation-type-edit' => [
                                        'label'   => _('txt-edit-organisation-type'),
                                        'route'   => 'zfcadmin/organisation/type/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Type::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'organisation-type-new'  => [
                                'label' => _('txt-create-new-organisation-type'),
                                'route' => 'zfcadmin/organisation/type/new',
                            ],
                        ],
                    ],
                ]
            ],
            'tools'        => [
                'pages' => [
                    'list-duplicate-organisations' => [
                        'label' => _('txt-nav-list-duplicate-organisations'),
                        'order' => 30,
                        'route' => 'zfcadmin/organisation/list/duplicate',
                    ],
                    'list-inactive-organisations'  => [
                        'label' => _('txt-nav-list-inactive-organisations'),
                        'order' => 30,
                        'route' => 'zfcadmin/organisation/list/inactive',
                    ],
                    'financial-check'              => [
                        'label' => _('txt-nav-financial-check'),
                        'order' => 30,
                        'route' => 'zfcadmin/organisation/list/no-financial',
                    ],
                    'parent-financial-check'       => [
                        'label' => _('txt-nav-parent-financial-check'),
                        'order' => 30,
                        'route' => 'zfcadmin/parent/list/no-financial',
                    ],
                    'parent-list-no-member'        => [
                        'label' => _('txt-nav-parent-list-no-member-no-doa'),
                        'order' => 30,
                        'route' => 'zfcadmin/parent/list/no-member',
                    ],
                    'pending-organisation-updates' => [
                        'label' => _('txt-nav-pending-organisation-updates'),
                        'order' => 30,
                        'route' => 'zfcadmin/organisation/update/pending',
                        'pages' => [
                            'update' => [
                                'route'   => 'zfcadmin/organisation/update/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Entity\Update::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\UpdateLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit' => [
                                        'label'   => _('txt-edit-update'),
                                        'route'   => 'zfcadmin/organisation/update/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Entity\Update::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\UpdateLabel::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ]
                    ],
                ]
            ]
        ],
    ],
];
