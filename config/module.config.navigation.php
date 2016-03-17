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
                'route'    => 'zfcadmin/organisation/list',
                'resource' => 'zfcadmin',
                'pages'    => [
                    'organisations' => [
                        'label' => "txt-organisation-list",
                        'route' => 'zfcadmin/organisation/list',
                        'pages' => [
                            'organisation'  => [
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
                                    'edit'  => [
                                        'label'   => _('txt-nav-edit'),
                                        'route'   => 'zfcadmin/organisation/edit',
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
                    'financial-check'     => [
                        'label' => _("txt-financial-check"),
                        'route' => 'zfcadmin/organisation/financial/no-financial',
                    ],
                    'vat-check'     => [
                        'label' => _("txt-financial-organisations"),
                        'route' => 'zfcadmin/organisation/financial/list',
                    ],
                ],
            ],
        ],
    ],
];
