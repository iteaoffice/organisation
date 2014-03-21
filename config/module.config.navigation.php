<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
return array(
    'navigation' => array(
        'community' => array(),
        'admin'     => array(
            // And finally, here is where we define our page hierarchy
            'organisation' => array(
                'label'    => _("txt-organisation-admin"),
                'route'    => 'zfcadmin/organisation-manager/list',
                'resource' => 'zfcadmin',
                'pages'    => array(
                    'organisations' => array(
                        'label' => "txt-organisations",
                        'route' => 'zfcadmin/organisation-manager/list',
                    ),
                ),
            ),
        ),
    ),
);
