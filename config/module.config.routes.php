<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Project
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */
use Organisation\Controller;

return [
    'router' => [
        'routes' => [
            'assets'       => [
                'type'          => 'Literal',
                'options'       => [
                    'route'    => '/assets/' . (defined("ITEAOFFICE_HOST") ? ITEAOFFICE_HOST : 'test'),
                    'defaults' => [
                        'controller' => Controller\OrganisationController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'organisation-logo' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => "/organisation-logo/[:id]-[:hash].[:ext]",
                            'defaults' => [
                                //Explicitly add the controller here as the assets are collected
                                'controller' => Controller\OrganisationController::class,
                                'action'     => 'logo',
                            ],
                        ],
                    ],
                ],
            ],
            'organisation' => [
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => [
                    'route'    => '/organisation',
                    'defaults' => [
                        'controller' => Controller\OrganisationController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'json'   => [
                        'type'         => 'Segment',
                        'options'      => [
                            'route'    => '/json',
                            'defaults' => [
                                'controller' => Controller\JsonController::class,
                                'action'     => 'get-branch',
                            ],
                        ],
                        'child_routes' => [
                            'get-branches' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/get-branches.json',
                                    'defaults' => [
                                        'controller' => Controller\JsonController::class,
                                        'action'     => 'get-branches',
                                    ],
                                    'query'    => [
                                        'organisationId' => null,
                                    ],
                                ],
                            ],
                            'check-vat'    => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/check-vat[/vat-:vat].json',
                                    'defaults' => [
                                        'controller' => Controller\JsonController::class,
                                        'action'     => 'check-vat',
                                    ],
                                    'query'    => [
                                        'financialId' => null,
                                    ],
                                ],
                            ],
                        ],

                    ],
                    'search' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/search',
                            'defaults' => [
                                'action' => 'search',
                            ],
                        ],
                    ],
                    'logo'   => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => '/logo/[:id].[:ext]',
                            'constraints' => [
                                'id' => '\d+',
                            ],
                            'defaults'    => [
                                'action' => 'logo',
                            ],
                        ],
                    ],
                ],
            ],
            'zfcadmin'     => [
                'child_routes' => [
                    'organisation'      => [
                        'type'          => 'Segment',
                        'priority'      => 1000,
                        'options'       => [
                            'route'    => '/organisation',
                            'defaults' => [
                                'controller' => Controller\OrganisationAdminController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'list'            => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'new'             => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'view'            => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/view/[:id][/f-:encodedFilter][/page-:page].html',
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
                            'search-form'     => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/search-form.html',
                                    'defaults' => [
                                        'action' => 'search-form',
                                    ],
                                ],
                            ],
                            'financial'       => [
                                'type'          => 'Segment',
                                'priority'      => 1000,
                                'options'       => [
                                    'route'    => '/financial',
                                    'defaults' => [
                                        'controller' => Controller\OrganisationFinancialController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes'  => [
                                    'list' => [
                                        'type'     => 'Segment',
                                        'priority' => 1000,
                                        'options'  => [
                                            'route'    => '/list[/:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                    ],

                                    'check'        => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/check/[:id].html',
                                            'defaults' => [
                                                'action' => 'check',
                                            ],
                                        ],
                                    ],
                                    'edit'         => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                    'no-financial' => [
                                        'type'     => 'Segment',
                                        'priority' => 1000,
                                        'options'  => [
                                            'route'    => '/no-financial.html',
                                            'defaults' => [
                                                'action' => 'no-financial',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'organisation-type' => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/organisation-type',
                            'defaults' => [
                                'controller' => Controller\OrganisationTypeController::class,
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
                    'parent'            => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/parent',
                            'defaults' => [
                                'controller' => Controller\ParentController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'list'   => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'import' => [
                                'type'         => 'Literal',
                                'options'      => [
                                    'route'    => '/import',
                                    'defaults' => [
                                        'action' => 'import',
                                    ],
                                ],
                                'child_routes' => [
                                    'parent' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/parent.html',
                                            'defaults' => [
                                                'action' => 'import-parent',
                                            ],
                                        ],
                                    ],
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

                            'new'                                      => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/new[/organisation-:organisationId].html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'edit'                                     => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'add-organisation'                         => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/add-organisation/[:id][/organisation-:organisationId].html',
                                    'defaults' => [
                                        'action' => 'add-organisation',
                                    ],
                                ],
                            ],
                            'edit-financial'                           => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit-financial/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit-financial',
                                    ],
                                ],
                            ],
                            'view'                                     => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'overview-variable-contribution'           => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/overview-variable-contribution/[:id]/year-[:year]/period-[:period].html',
                                    'defaults' => [
                                        'action' => 'overview-variable-contribution',
                                    ],
                                ],
                            ],
                            'overview-variable-contribution-pdf'       => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/overview-variable-contribution/[:id]/year-[:year]/period-[:period].pdf',
                                    'defaults' => [
                                        'action' => 'overview-variable-contribution-pdf',
                                    ],
                                ],
                            ],
                            'overview-extra-variable-contribution'     => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/overview-extra-variable-contribution/[:id]/year-[:year]/period-[:period].html',
                                    'defaults' => [
                                        'action' => 'overview-extra-variable-contribution',
                                    ],
                                ],
                            ],
                            'overview-extra-variable-contribution-pdf' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/overview-extra-variable-contribution/[:id]/year-[:year]/period-[:period].pdf',
                                    'defaults' => [
                                        'action' => 'overview-extra-variable-contribution-pdf',
                                    ],
                                ],
                            ],
                            'organisation'                             => [
                                'type'          => 'Literal',
                                'options'       => [
                                    'route'    => '/organisation',
                                    'defaults' => [
                                        'controller' => Controller\ParentOrganisationController::class,
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
                                ],
                            ],
                            'doa'                                      => [
                                'type'          => 'Literal',
                                'options'       => [
                                    'route'    => '/doa',
                                    'defaults' => [
                                        'controller' => Controller\ParentDoaController::class,
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
                        ],
                    ],
                    'parent-type'       => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/parent-type',
                            'defaults' => [
                                'controller' => Controller\ParentTypeController::class,
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
                    'parent-status'     => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/parent-status',
                            'defaults' => [
                                'controller' => Controller\ParentStatusController::class,
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
];
