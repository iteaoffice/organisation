<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Project
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
return array(
    'router' => array(
        'routes' => array(
            'organisation_shortcut' => array(
                'type'     => 'Segment',
                'priority' => -1000,
                'options'  => array(
                    'route'       => 'o/:id',
                    'constraints' => array(
                        'id' => '\d+',
                    ),
                    'defaults'    => array(
                        'controller' => 'organisation-index',
                        'action'     => 'organisationRedirect',
                    ),
                ),
            ),
            'assets'                => array(
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => array(
                    'route'    => '/assets/' . DEBRANOVA_HOST,
                    'defaults' => array(
                        'controller' => 'index',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'organisation-logo' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => "/organisation-logo/[:hash].[:ext]",
                            'defaults' => array(
                                'action' => 'display',
                            ),
                        ),
                    ),
                ),
            ),
            'organisation'          => array(
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
                    'organisations' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/list[/page/:page].html',
                            'defaults' => array(
                                'action' => 'organisations',
                            ),
                        ),
                    ),
                    'organisation'  => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => '/[:docRef].html',
                            'constraints' => array(
                                'docRef' => '\d+',
                            ),
                            'defaults'    => array(
                                'action' => 'organisation',
                            ),
                        ),
                    ),
                    'logo'          => array(
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
                    'edit'          => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/edit/[:entity]/[:id].html',
                            'defaults' => array(
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            'zfcadmin'              => array(
                'child_routes' => array(
                    'organisation-manager' => array(
                        'type'          => 'Segment',
                        'priority'      => 1000,
                        'options'       => array(
                            'route'    => '/organisation-manager',
                            'defaults' => array(
                                'controller' => 'organisation-manager',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes'  => array(
                            'new'    => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/new/:entity',
                                    'defaults' => array(
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit'   => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/edit/:entity/:id',
                                    'defaults' => array(
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/delete/:entity/:id',
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
