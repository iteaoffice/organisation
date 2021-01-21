<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

use Organisation\Controller;

return [
    'router' => [
        'routes' => [
            'image'        => [
                'child_routes' => [
                    'organisation-logo'        => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/o/[:id]-[:last-update].[:ext]',
                            'defaults' => [
                                //Explicitly add the controller here as the assets are collected
                                'controller' => Controller\ImageController::class,
                                'action'     => 'organisation-logo',
                            ],
                        ],
                    ],
                    'organisation-update-logo' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/u/[:id]-[:last-update].[:ext]',
                            'defaults' => [
                                // Explicitly add the controller here as the assets are collected
                                'controller' => Controller\ImageController::class,
                                'action'     => 'organisation-update-logo',
                            ],
                        ],
                    ],
                ],
            ],
            'organisation' => [
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => [
                    'route' => '/organisation',
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'json' => [
                        'type'         => 'Segment',
                        'options'      => [
                            'route'    => '/json',
                            'defaults' => [
                                'controller' => Controller\JsonController::class,
                                'action'     => 'get-branch',
                            ],
                        ],
                        'child_routes' => [
                            'get-branches'  => [
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
                            'check-vat'     => [
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
                            'search'        => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/search.json',
                                    'defaults' => [
                                        'controller' => Controller\JsonController::class,
                                        'action'     => 'search',
                                    ],
                                ],
                            ],
                            'search-parent' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/search-parent.json',
                                    'defaults' => [
                                        'controller' => Controller\JsonController::class,
                                        'action'     => 'search-parent',
                                    ],
                                ],
                            ],
                        ],

                    ],
                    'logo' => [
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
            'community'    => [
                'child_routes' => [
                    'organisation' => [
                        'type'          => 'Segment',
                        'priority'      => 1001,
                        'options'       => [
                            'route' => '/organisation',
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'update' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/update/[:organisationId].html',
                                    'constraints' => [
                                        'organisationId' => '\d+',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\UpdateController::class,
                                        'action'     => 'new',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'zfcadmin'     => [
                'child_routes' => [
                    'organisation' => [
                        'type'          => 'Segment',
                        'priority'      => 1000,
                        'options'       => [
                            'route'    => '/organisation',
                            'defaults' => [
                                'controller' => Controller\Organisation\AdminController::class,
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
                            'list-duplicate'  => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/list/duplicate[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list-duplicate',
                                    ],
                                ],
                            ],
                            'list-inactive'   => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/list/inactive.html',
                                    'defaults' => [
                                        'action' => 'list-inactive',
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
                            'manage-web'      => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/manage-web/[:id].html',
                                    'defaults' => [
                                        'action' => 'manage-web',
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
                            'financial'       => [
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
                            'note'            => [
                                'type'          => 'Segment',
                                'options'       => [
                                    'route'    => '/note',
                                    'defaults' => [
                                        'controller' => Controller\NoteController::class,
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
                            'merge'           => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/merge/[:sourceId]/into/[:targetId].html',
                                    'defaults' => [
                                        'action' => 'merge',
                                    ],
                                ],
                            ],
                            'update'          => [
                                'type'          => 'Segment',
                                'options'       => [
                                    'route'    => '/update',
                                    'defaults' => [
                                        'controller' => Controller\UpdateManagerController::class,
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
                            'type'            => [
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
                            'selection'       => [
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
                    'parent'       => [
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
                            'list'                                     => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'list-no-member'                           => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/list/no-member-doa[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list-no-member',
                                    ],
                                ],
                            ],
                            'list-no-member-export'                    => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/list/no-member-doa-export[/f-:encodedFilter].csv',
                                    'defaults' => [
                                        'action' => 'list-no-member-export',
                                    ],
                                ],
                            ],
                            'import'                                   => [
                                'type'         => 'Literal',
                                'options'      => [
                                    'route'    => '/import',
                                    'defaults' => [
                                        'action' => 'import',
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
                            'financial'                                => [
                                'type'         => 'Literal',
                                'options'      => [
                                    'route'    => '/financial',
                                    'defaults' => [
                                        'action'     => 'not-found',
                                        'controller' => Controller\Parent\FinancialController::class
                                    ],
                                ],
                                'child_routes' => [
                                    'new'          => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/new/parent-[:parentId].html',
                                            'defaults' => [
                                                'action' => 'new',
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
                                    'route'    => '/overview-variable-contribution/[:id]/year-[:year]/program-[:program].html',
                                    'defaults' => [
                                        'action' => 'overview-variable-contribution',
                                    ],
                                ],
                            ],
                            'overview-variable-contribution-pdf'       => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/overview-variable-contribution/[:id]/year-[:year]/program-[:program].pdf',
                                    'defaults' => [
                                        'action' => 'overview-variable-contribution-pdf',
                                    ],
                                ],
                            ],
                            'overview-extra-variable-contribution'     => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/overview-extra-variable-contribution/[:id]/year-[:year]/program-[:program].html',
                                    'defaults' => [
                                        'action' => 'overview-extra-variable-contribution',
                                    ],
                                ],
                            ],
                            'overview-extra-variable-contribution-pdf' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/overview-extra-variable-contribution/[:id]/year-[:year]/program-[:program].pdf',
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
                            'doa'                                      => [
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
                            'type'                                     => [
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
