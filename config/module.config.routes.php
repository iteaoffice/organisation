<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
return array(
    'router'       => array(
        'routes' => array(
            'organisation_shortcut' => array(
                'type'     => 'Segment',
                'priority' => -1000,
                'options'  => array(
                    'route'       => 'l/:id',
                    'constraints' => array(
                        'id' => '\d+',
                    ),
                    'defaults'    => array(
                        'controller' => 'organisation',
                        'action'     => 'organisationRedirect',
                    ),
                ),
            ),
            'organisation'          => array(
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => array(
                    'route'    => '/organisation',
                    'defaults' => array(
                        'controller' => 'organisation',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'organisations'       => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/list.html',
                            'defaults' => array(
                                'action' => 'organisations',
                            ),
                        ),
                    ),
                    'organisation'        => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => '/view/[:id].html',
                            'constraints' => array(
                                'id' => '\d+',
                            ),
                            'defaults'    => array(
                                'action' => 'organisation',
                            ),
                        ),
                    ),
                    'facilities'     => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/facilities.html',
                            'defaults' => array(
                                'action' => 'facilities',
                            ),
                        ),
                    ),
                    'facility'       => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => '/facility/[:id].html',
                            'constraints' => array(
                                'id' => '\d+',
                            ),
                            'defaults'    => array(
                                'action' => 'facility',
                            ),
                        ),
                    ),
                    'areas'          => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/areas.html',
                            'defaults' => array(
                                'action' => 'areas',
                            ),
                        ),
                    ),
                    'area'           => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => '/area/[:id].html',
                            'constraints' => array(
                                'id' => '\d+',
                            ),
                            'defaults'    => array(
                                'action' => 'area',
                            ),
                        ),
                    ),
                    'area2s'         => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/area2s.html',
                            'defaults' => array(
                                'action' => 'area2s',
                            ),
                        ),
                    ),
                    'area2'          => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => '/area2/[:id].html',
                            'constraints' => array(
                                'id' => '\d+',
                            ),
                            'defaults'    => array(
                                'action' => 'area2',
                            ),
                        ),
                    ),
                    'sub-areas'      => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/sub-areas.html',
                            'defaults' => array(
                                'action' => 'sub-areas',
                            ),
                        ),
                    ),
                    'sub-area'       => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => '/sub-area/[:id].html',
                            'constraints' => array(
                                'id' => '\d+',
                            ),
                            'defaults'    => array(
                                'action' => 'sub-area',
                            ),
                        ),
                    ),
                    'oper-areas'     => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/operation-areas.html',
                            'defaults' => array(
                                'action' => 'operAreas',
                            ),
                        ),
                    ),
                    'oper-area'      => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => '/oper-area/[:id].html',
                            'constraints' => array(
                                'id' => '\d+',
                            ),
                            'defaults'    => array(
                                'action' => 'oper-area',
                            ),
                        ),
                    ),
                    'oper-sub-areas' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/operation-sub-areas.html',
                            'defaults' => array(
                                'action' => 'oper-sub-areas',
                            ),
                        ),
                    ),
                    'oper-sub-area'  => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => '/oper-sub-area/[:id].html',
                            'constraints' => array(
                                'id' => '\d+',
                            ),
                            'defaults'    => array(
                                'action' => 'oper-sub-area',
                            ),
                        ),
                    ),
                    'edit'           => array(
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
            'zfcadmin'         => array(
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
                            'messages' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/messages.html',
                                    'defaults' => array(
                                        'action' => 'messages',
                                    ),
                                ),
                            ),
                            'message'  => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'       => '/message/[:id].html',
                                    'constraints' => array(
                                        'id' => '\d+',
                                    ),
                                    'defaults'    => array(
                                        'action' => 'message',
                                    ),
                                ),
                            ),
                            'new'      => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/new/:entity',
                                    'defaults' => array(
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit'     => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/edit/:entity/:id',
                                    'defaults' => array(
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete'   => array(
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
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
    ),
);
