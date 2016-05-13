<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
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
                        'label' => "txt-nav-organisation-list",
                        'order' => 10,
                        'route' => 'zfcadmin/organisation/list',
                        'pages' => [
                            'organisation' => [
                                'label'   => _("txt-nav-project-partner"),
                                'route'   => 'zfcadmin/organisation/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Organisation\Entity\Organisation::class
                                    ],
                                    'invokables' => [
                                        Organisation\Navigation\Invokable\OrganisationLabel::class
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
                        ]
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
                                    ]
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
                                ]
                            ],
                            'organisation-new'       => [
                                'label' => _("txt-create-new-organisation-type"),
                                'route' => 'zfcadmin/organisation-type/new',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
