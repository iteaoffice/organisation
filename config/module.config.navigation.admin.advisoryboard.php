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
            'advisory-board' => [
                'label' => _('txt-advisory-board'),
                'order' => 31,
                'uri'   => '#',
                'pages' => [
                    'city' => [
                        'label' => _('txt-nav-city-list'),
                        'order' => 20,
                        'route' => 'zfcadmin/advisory-board/city/list',
                        'pages' => [
                            'details' => [
                                'route'   => 'zfcadmin/advisory-board/city/details/general',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Entity\AdvisoryBoard\City::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\AdvisoryBoard\CityLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit' => [
                                        'label' => _('txt-edit-city'),
                                        'route' => 'zfcadmin/advisory-board/city/edit',
                                    ]
                                ]
                            ],
                            'new'     => [
                                'label' => _('txt-new-city'),
                                'route' => 'zfcadmin/advisory-board/city/new',
                            ],
                        ],
                    ],
                    'tender' => [
                        'label' => _('txt-nav-tender-list'),
                        'order' => 20,
                        'route' => 'zfcadmin/advisory-board/tender/list',
                        'pages' => [
                            'details' => [
                                'route'   => 'zfcadmin/advisory-board/tender/details/general',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Entity\AdvisoryBoard\Tender::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\AdvisoryBoard\TenderLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit' => [
                                        'label' => _('txt-edit-tender'),
                                        'route' => 'zfcadmin/advisory-board/tender/edit',
                                    ]
                                ]
                            ],
                            'new'     => [
                                'label' => _('txt-new-tender'),
                                'route' => 'zfcadmin/advisory-board/tender/new',
                            ],
                        ],
                    ],
                    'solution' => [
                        'label' => _('txt-nav-solution-list'),
                        'order' => 20,
                        'route' => 'zfcadmin/advisory-board/solution/list',
                        'pages' => [
                            'details' => [
                                'route'   => 'zfcadmin/advisory-board/solution/details/general',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Entity\AdvisoryBoard\Solution::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\AdvisoryBoard\SolutionLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit' => [
                                        'label' => _('txt-edit-solution'),
                                        'route' => 'zfcadmin/advisory-board/solution/edit',
                                    ]
                                ]
                            ],
                            'new'     => [
                                'label' => _('txt-new-solution'),
                                'route' => 'zfcadmin/advisory-board/solution/new',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]
];
