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
    'navigation' => array(
        'default' => array(
            'organisation' => array(
                'label' => _("txt-organisation"),
                'route' => 'organisation',
                'pages' => array(
                    'organisations' => array(
                        'label'     => _("txt-list-organisations"),
                        'route'     => 'organisation/organisations',
                        'resource'  => 'organisation',
                        'privilege' => 'listings',
                    ),

                ),
            ),
            'admin'       => array(
                'pages' => array(
                    'messages' => array(
                        'label' => _('txt-messages'),
                        'route' => 'zfcadmin/organisation-manager/messages',
                    ),
                ),
            ),
        ),
    ),
);
