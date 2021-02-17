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
                    'parent' => [
                        'label' => _('txt-nav-parent-list'),
                        'order' => 20,
                        'route' => 'zfcadmin/parent/list/parent',
                        'pages' => [
                            'details'        => [
                                'route'   => 'zfcadmin/parent/details/general',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Entity\ParentEntity::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\ParentLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'organisations'                        => [
                                        'label'   => _('txt-organisations'),
                                        'route'   => 'zfcadmin/parent/details/organisations',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\ParentEntity::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\ParentLabel::class,
                                            ],
                                        ],
                                        'pages'   => [
                                            'organisation' => [
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
                                        ]
                                    ],
                                    'doas'                                 => [
                                        'label'   => _('txt-doas'),
                                        'route'   => 'zfcadmin/parent/details/doas',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\ParentEntity::class,
                                            ],
                                        ],
                                    ],
                                    'financial'                            => [
                                        'label'   => _('txt-financial'),
                                        'route'   => 'zfcadmin/parent/details/financial',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\ParentEntity::class,
                                            ],
                                        ],
                                    ],
                                    'invoices'                             => [
                                        'label'   => _('txt-invoices'),
                                        'route'   => 'zfcadmin/parent/details/invoices',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\ParentEntity::class,
                                            ],
                                        ],
                                    ],
                                    'edit'                                 => [
                                        'label'   => _('txt-edit-parent'),
                                        'route'   => 'zfcadmin/parent/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\ParentEntity::class,
                                            ],
                                        ],
                                    ],
                                    'edit-financial'                       => [
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
                                    'new-financial'                        => [
                                        'label'   => _('txt-new-parent-financial'),
                                        'route'   => 'zfcadmin/parent/financial/new',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Entity\ParentEntity::class,
                                            ],
                                            'routeParam' => [
                                                'id' => 'parentId'
                                            ]
                                        ],
                                    ],
                                    'add-organisation'                     => [
                                        'label'   => _('txt-parent-add-organisation'),
                                        'route'   => 'zfcadmin/parent/add-organisation',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\ParentEntity::class,
                                            ],
                                        ],
                                    ],
                                    'overview-variable-contribution'       => [
                                        'label'   => _('txt-overview-variable-contribution'),
                                        'route'   => 'zfcadmin/parent/contribution/variable',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\ParentEntity::class,
                                            ],
                                        ],
                                    ],
                                    'overview-extra-variable-contribution' => [
                                        'label'   => _('txt-overview-extra-variable-contribution'),
                                        'route'   => 'zfcadmin/parent/contribution/extra-variable',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Entity\ParentEntity::class,
                                            ],
                                        ],
                                    ],

                                    'upload-doa' => [
                                        'label'   => _('txt-upload-doa'),
                                        'route'   => 'zfcadmin/parent/doa/upload',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Entity\ParentEntity::class,
                                            ],
                                            'routeParam' => [
                                                'id' => 'parentId',
                                            ],
                                        ],
                                    ],
                                    'edit-doa'   => [
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
                ],
            ],
            'config'       => [
                'pages' => [
                    'parent-type-list' => [
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
        ],
    ],
];
