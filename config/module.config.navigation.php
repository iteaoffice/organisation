<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */
return [
    'navigation' => [
        'community' => [],
        'admin'     => [
            // And finally, here is where we define our page hierarchy
            'organisation' => [
                'label'    => _("txt-organisation-admin"),
                'order'    => 20,
                'route'    => 'zfcadmin/organisation/list',
                'resource' => 'zfcadmin',
                'pages'    => [
                    'organisation-list'           => [
                        'label' => _("txt-nav-organisation-list"),
                        'order' => 10,
                        'route' => 'zfcadmin/organisation/list',
                        'pages' => [
                            'organisation' => [
                                'label'   => _("txt-nav-project-partner"),
                                'route'   => 'zfcadmin/organisation/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Organisation\Entity\Organisation::class,
                                    ],
                                    'invokables' => [
                                        Organisation\Navigation\Invokable\OrganisationLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit'            => [
                                        'label'   => _('txt-nav-edit'),
                                        'route'   => 'zfcadmin/organisation/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => \Organisation\Entity\Organisation::class,
                                            ],
                                        ],
                                    ],
                                    'financial'       => [
                                        'label'   => _('txt-nav-edit-financial'),
                                        'route'   => 'zfcadmin/organisation/financial/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => \Organisation\Entity\Organisation::class,
                                            ],
                                        ],
                                    ],
                                    'add-affiliation' => [
                                        'label'   => _('txt-nav-add-affiliation'),
                                        'route'   => 'zfcadmin/organisation/add-affiliation',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => \Organisation\Entity\Organisation::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'financial-organisation-list' => [
                        'label' => _("txt-nav-financial-organisation-list"),
                        'order' => 20,
                        'route' => 'zfcadmin/organisation/financial/list',
                    ],
                    'financial-check'             => [
                        'label' => _("txt-nav-financial-check"),
                        'order' => 30,
                        'route' => 'zfcadmin/organisation/financial/no-financial',
                    ],
                    'parent-list'                 => [
                        'label' => _("txt-nav-parent-list"),
                        'order' => 50,
                        'route' => 'zfcadmin/parent/list',
                        'pages' => [
                            'parent-view' => [
                                'route'   => 'zfcadmin/parent/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Organisation\Entity\OParent::class,
                                    ],
                                    'invokables' => [
                                        Organisation\Navigation\Invokable\ParentLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'parent-edit'                          => [
                                        'label'   => _("txt-edit-parent"),
                                        'route'   => 'zfcadmin/parent/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Organisation\Entity\OParent::class,
                                            ],
                                        ],
                                    ],
                                    'parent-edit-financial'                => [
                                        'label'   => _("txt-edit-financial-parent"),
                                        'route'   => 'zfcadmin/parent/edit-financial',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Organisation\Entity\OParent::class,
                                            ],
                                        ],
                                    ],
                                    'parent-add-organisation'              => [
                                        'label'   => _("txt-parent-add-organisation"),
                                        'route'   => 'zfcadmin/parent/add-organisation',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Organisation\Entity\OParent::class,
                                            ],
                                        ],
                                    ],
                                    'overview-variable-contribution'       => [
                                        'label'   => _("txt-overview-variable-contribution"),
                                        'route'   => 'zfcadmin/parent/overview-variable-contribution',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Organisation\Entity\OParent::class,
                                            ],
                                        ],
                                    ],
                                    'overview-extra-variable-contribution' => [
                                        'label'   => _("txt-overview-extra-variable-contribution"),
                                        'route'   => 'zfcadmin/parent/overview-extra-variable-contribution',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Organisation\Entity\OParent::class,
                                            ],
                                        ],
                                    ],
                                    'organisation'                         => [
                                        'route'   => 'zfcadmin/parent/organisation/view',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Organisation\Entity\Parent\Organisation::class,
                                            ],
                                            'invokables' => [
                                                Organisation\Navigation\Invokable\OrganisationLabel::class,
                                            ],
                                        ],
                                        'pages'   => [
                                            'edit-organisation' => [
                                                'label'   => _("txt-edit-organisation"),
                                                'route'   => 'zfcadmin/parent/organisation/edit',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities' => [
                                                        'id' => Organisation\Entity\Parent\Organisation::class,
                                                    ],
                                                ],
                                            ],
                                            'add-affiliation'   => [
                                                'label'   => _("txt-add-affiliation"),
                                                'route'   => 'zfcadmin/parent/organisation/add-affiliation',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities' => [
                                                        'id' => Organisation\Entity\Parent\Organisation::class,
                                                    ],
                                                ],
                                            ],

                                        ],
                                    ],
                                    'upload-doa'                           => [
                                        'label'   => _("txt-upload-doa"),
                                        'route'   => 'zfcadmin/parent/doa/upload',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Organisation\Entity\OParent::class,
                                            ],
                                            'routeParam' => [
                                                'id' => 'parentId',
                                            ],
                                        ],
                                    ],
                                    'edit-doa'                             => [
                                        'label'   => _("txt-edit-doa"),
                                        'route'   => 'zfcadmin/parent/doa/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Organisation\Entity\Parent\Doa::class,
                                            ],
                                            'invokables' => [
                                                Organisation\Navigation\Invokable\Parent\DoaLabel::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'parent-new'  => [
                                'label' => _("txt-create-new-parent"),
                                'route' => 'zfcadmin/parent/new',
                            ],
                            //                            'parent-import' => [
                            //                                'label' => _("txt-import-parents"),
                            //                                'route' => 'zfcadmin/parent/import',
                            //                            ],
                        ],
                    ],

                ],
            ],
            'management'   => [
                'pages' => [
                    'organisation-type-list' => [
                        'label' => _("txt-organisation-type-list"),
                        'route' => 'zfcadmin/organisation-type/list',
                        'pages' => [
                            'organisation-type-view' => [
                                'route'   => 'zfcadmin/organisation-type/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Organisation\Entity\Type::class,
                                    ],
                                    'invokables' => [
                                        Organisation\Navigation\Invokable\TypeLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'organisation-type-edit' => [
                                        'label'   => _("txt-edit-organisation-type"),
                                        'route'   => 'zfcadmin/organisation-type/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Organisation\Entity\Type::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'organisation-new'       => [
                                'label' => _("txt-create-new-organisation-type"),
                                'route' => 'zfcadmin/organisation-type/new',
                            ],
                        ],
                    ],
                    'parent-type-list'       => [
                        'label' => _("txt-parent-type-list"),
                        'route' => 'zfcadmin/parent-type/list',
                        'pages' => [
                            'parent-type-view' => [
                                'route'   => 'zfcadmin/parent-type/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Organisation\Entity\Parent\Type::class,
                                    ],
                                    'invokables' => [
                                        Organisation\Navigation\Invokable\Parent\TypeLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'parent-type-edit' => [
                                        'label'   => _("txt-edit-parent-type"),
                                        'route'   => 'zfcadmin/parent-type/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Organisation\Entity\Parent\Type::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'parent-new'       => [
                                'label' => _("txt-create-new-parent-type"),
                                'route' => 'zfcadmin/parent-type/new',
                            ],
                        ],
                    ],
                    'parent-status-list'     => [
                        'label' => _("txt-parent-status-list"),
                        'route' => 'zfcadmin/parent-status/list',
                        'pages' => [
                            'parent-status-view' => [
                                'route'   => 'zfcadmin/parent-status/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Organisation\Entity\Parent\Status::class,
                                    ],
                                    'invokables' => [
                                        Organisation\Navigation\Invokable\Parent\StatusLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'parent-status-edit' => [
                                        'label'   => _("txt-edit-parent-status"),
                                        'route'   => 'zfcadmin/parent-status/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Organisation\Entity\Parent\Status::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'parent-new'         => [
                                'label' => _("txt-create-new-parent-status"),
                                'route' => 'zfcadmin/parent-status/new',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
