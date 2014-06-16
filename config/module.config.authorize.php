<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */
namespace Organisation;

return [
    'bjyauthorize' => [
        // resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                'organisation' => [],
            ],
        ],
        /* rules can be specified here with the format:
         * [roles (array] , resource, [privilege (array|string], assertion]]
         * assertions will be loaded using the service manager and must implement
         * Zend\Acl\Assertion\AssertionInterface.
         * *if you use assertions, define them using the service manager!*
         */
        'rule_providers'     => [
            'BjyAuthorize\Provider\Rule\Config' => [
                'allow' => [
                    // allow guests and users (and admins, through inheritance]
                    // the "wear" privilege on the resource "pants"d
                    [['public'], 'organisation', ['listings', 'view']],
                    [['office'], 'organisation', ['edit', 'new', 'delete']]
                ],
                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny'  => [ // ...
                ],
            ],
        ],
        /* Currently, only controller and route guards exist
         */
        'guards'             => [
            /* If this guard is specified here (i.e. it is enabled], it will block
             * access to all routes unless they are specified here.
             */
            'BjyAuthorize\Guard\Route' => [
                ['route' => 'assets/organisation-logo', 'roles' => []],
                ['route' => 'organisation/logo', 'roles' => []],
                ['route' => 'organisation/search', 'roles' => []],
                ['route' => 'organisation-manager/list', 'roles' => ['office']],
                ['route' => 'organisation-manager/edit', 'roles' => ['office']],
                ['route' => 'organisation-manager/new', 'roles' => ['office']],
            ],
        ],
    ],
];
