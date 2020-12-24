<?php

/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

namespace Organisation;

return [
    'navigation' => [
        'community' => [],
        'admin'     => [
            // And finally, here is where we define our page hierarchy
            'organisation' => [
                'label'    => _('txt-organisation-admin'),
                'order'    => 30,
                'route'    => 'zfcadmin/organisation/list',
                'resource' => 'zfcadmin',
                'pages'    => [
                    'organisation'           => [
                        'label' => _('txt-nav-organisation-list'),
                        'order' => 10,
                        'route' => 'zfcadmin/organisation/list',
                        'pages' => [
                            'organisation'     => [
                                'route'   => 'zfcadmin/organisation/view',
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
                                    'edit'            => [
                                        'label'   => _('txt-nav-edit'),
                                        'route'   => 'zfcadmin/organisation/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                        ],
                                    ],
                                    'manage-web'      => [
                                        'label'   => _('txt-nav-manage-web'),
                                        'route'   => 'zfcadmin/organisation/manage-web',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                        ],
                                    ],
                                    'financial'       => [
                                        'label'   => _('txt-nav-edit-financial'),
                                        'route'   => 'zfcadmin/organisation/financial/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                        ],
                                    ],
                                    'add-affiliation' => [
                                        'label'   => _('txt-nav-add-affiliation'),
                                        'route'   => 'zfcadmin/organisation/add-affiliation',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Organisation::class,
                                            ],
                                        ],
                                    ],
                                    'add-note'        => [
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
                                    'edit-note'       => [
                                        'label'   => _('txt-nav-edit-note'),
                                        'route'   => 'zfcadmin/organisation/note/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Note::class,
                                            ],
                                        ],
                                    ],
                                    'merge'           => [
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
                            'organisation-new' => [
                                'label'   => _('txt-add-organisation'),
                                'route'   => 'zfcadmin/organisation/new',
                                'visible' => false,
                            ],
                        ],
                    ],
                    'parent-list'                 => [
                        'label' => _('txt-nav-parent-list'),
                        'order' => 20,
                        'route' => 'zfcadmin/parent/list',
                        'pages' => [
                            'parent-view'    => [
                                'route'   => 'zfcadmin/parent/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Entity\OParent::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\ParentLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'parent-edit'                          => [
                                        'label'   => _('txt-edit-parent'),
                                        'route'   => 'zfcadmin/parent/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\OParent::class,
                                            ],
                                        ],
                                    ],
                                    'edit-parent-financial'                => [
                                        'label'   => _('txt-edit-parent-financial'),
                                        'route'   => 'zfcadmin/parent/financial/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Entity\Parent\Financial::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\Parent\FinancialLabel::class,
                                            ],
                                        ],
                                    ],
                                    'new-parent-financial'                 => [
                                        'label'   => _('txt-new-parent-financial'),
                                        'route'   => 'zfcadmin/parent/financial/new',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Entity\OParent::class,
                                            ],
                                            'routeParam' => [
                                                'id' => 'parentId'
                                            ]
                                        ],
                                    ],
                                    'parent-add-organisation'              => [
                                        'label'   => _('txt-parent-add-organisation'),
                                        'route'   => 'zfcadmin/parent/add-organisation',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\OParent::class,
                                            ],
                                        ],
                                    ],
                                    'overview-variable-contribution'       => [
                                        'label'   => _('txt-overview-variable-contribution'),
                                        'route'   => 'zfcadmin/parent/overview-variable-contribution',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\OParent::class,
                                            ],
                                        ],
                                    ],
                                    'overview-extra-variable-contribution' => [
                                        'label'   => _('txt-overview-extra-variable-contribution'),
                                        'route'   => 'zfcadmin/parent/overview-extra-variable-contribution',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\OParent::class,
                                            ],
                                        ],
                                    ],
                                    'organisation'                         => [
                                        'route'   => 'zfcadmin/parent/organisation/view',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Entity\Parent\Organisation::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\Parent\OrganisationLabel::class,
                                            ],
                                        ],
                                        'pages'   => [
                                            'edit-organisation' => [
                                                'label'   => _('txt-edit-organisation'),
                                                'route'   => 'zfcadmin/parent/organisation/edit',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities' => [
                                                        'id' => Entity\Parent\Organisation::class,
                                                    ],
                                                ],
                                            ],
                                            'add-affiliation'   => [
                                                'label'   => _('txt-add-affiliation'),
                                                'route'   => 'zfcadmin/parent/organisation/add-affiliation',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities' => [
                                                        'id' => Entity\Parent\Organisation::class,
                                                    ],
                                                ],
                                            ],
                                            'merge'             => [
                                                'label'   => _('txt-merge'),
                                                'route'   => 'zfcadmin/parent/organisation/merge',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities' => [
                                                        'id' => Entity\Parent\Organisation::class,
                                                    ],
                                                ],
                                            ],

                                        ],
                                    ],
                                    'upload-doa'                           => [
                                        'label'   => _('txt-upload-doa'),
                                        'route'   => 'zfcadmin/parent/doa/upload',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Entity\OParent::class,
                                            ],
                                            'routeParam' => [
                                                'id' => 'parentId',
                                            ],
                                        ],
                                    ],
                                    'edit-doa'                             => [
                                        'label'   => _('txt-edit-doa'),
                                        'route'   => 'zfcadmin/parent/doa/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Entity\Parent\Doa::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\Parent\DoaLabel::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'parent-new'     => [
                                'label' => _('txt-create-new-parent'),
                                'route' => 'zfcadmin/parent/new',
                            ],
                            'import-project' => [
                                'label' => _('txt-import-project'),
                                'route' => 'zfcadmin/parent/import/project',
                            ],
                        ],
                    ],
                    'financial-organisation-list' => [
                        'label' => _('txt-nav-financial-organisation-list'),
                        'order' => 30,
                        'route' => 'zfcadmin/organisation/financial/list',
                    ],
                    'board'                       => [
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
                    'selection'                   => [
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
                    'parent-type-list'       => [
                        'label' => _('txt-parent-type-list'),
                        'route' => 'zfcadmin/parent/type/list',
                        'pages' => [
                            'parent-type-view' => [
                                'route'   => 'zfcadmin/parent/type/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Entity\Parent\Type::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\Parent\TypeLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'parent-type-edit' => [
                                        'label'   => _('txt-edit-parent-type'),
                                        'route'   => 'zfcadmin/parent/type/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\Parent\Type::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'parent-new'       => [
                                'label' => _('txt-create-new-parent-type'),
                                'route' => 'zfcadmin/parent/type/new',
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
                        'route' => 'zfcadmin/organisation/list-duplicate',
                    ],
                    'list-inactive-organisations'  => [
                        'label' => _('txt-nav-list-inactive-organisations'),
                        'order' => 30,
                        'route' => 'zfcadmin/organisation/list-inactive',
                    ],
                    'financial-check'              => [
                        'label' => _('txt-nav-financial-check'),
                        'order' => 30,
                        'route' => 'zfcadmin/organisation/financial/no-financial',
                    ],
                    'parent-financial-check'       => [
                        'label' => _('txt-nav-parent-financial-check'),
                        'order' => 30,
                        'route' => 'zfcadmin/parent/financial/no-financial',
                    ],
                    'parent-list-no-member'        => [
                        'label' => _('txt-nav-parent-list-no-member-no-doa'),
                        'order' => 30,
                        'route' => 'zfcadmin/parent/list-no-member',
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
