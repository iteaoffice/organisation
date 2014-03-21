<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Project
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
return array(
    'router' => array(
        'routes' => array(
            'assets'       => array(
                'type'          => 'Literal',
                'options'       => array(
                    'route'    => '/assets/' . DEBRANOVA_HOST,
                    'defaults' => array(
                        'controller' => 'organisation-index',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'organisation-logo' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => "/organisation-logo/[:id].[:ext]",
                            'defaults' => array(
                                //Explicitly add the controller here as the assets are collected
                                'controller' => 'organisation-index',
                                'action'     => 'logo',
                            ),
                        ),
                    ),
                ),
            ),
            'organisation' => array(
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => array(
                    'route'    => '/organisation',
                    'defaults' => array(
                        'controller' => 'organisation-index',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'search' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/search',
                            'defaults' => array(
                                'action' => 'search',
                            ),
                        ),
                    ),
                    'logo'   => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => '/logo/[:id].[:ext]',
                            'constraints' => array(
                                'id' => '\d+',
                            ),
                            'defaults'    => array(
                                'action' => 'logo',
                            ),
                        ),
                    ),
                ),
            ),
            'zfcadmin'     => array(
                'child_routes' => array(
                    'organisation-manager' => array(
                        'type'          => 'Segment',
                        'priority'      => 1000,
                        'options'       => array(
                            'route'    => '/organisation',
                            'defaults' => array(
                                'controller' => 'organisation-manager',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes'  => array(
                            'list'   => array(
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => array(
                                    'route'    => '/list[/:page].html',
                                    'defaults' => array(
                                        'action' => 'list',
                                    ),
                                ),
                            ),
                            'new'    => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/new.html',
                                    'defaults' => array(
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'view'   => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/view/[:id].html',
                                    'defaults' => array(
                                        'action' => 'view',
                                    ),
                                ),
                            ),
                            'edit'   => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => array(
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/delete/[:id].html',
                                    'defaults' => array(
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
