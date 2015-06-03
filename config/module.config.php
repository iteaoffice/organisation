<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
use Organisation\Acl\Assertion\Organisation as OrganisationAssertion;
use Organisation\Controller\ControllerInitializer;
use Organisation\Controller\JsonController;
use Organisation\Controller\OrganisationAdminController;
use Organisation\Controller\OrganisationController;
use Organisation\Controller\OrganisationManagerController;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Organisation\Service\ServiceInitializer;
use Organisation\View\Helper\CreateLogoFromArray;
use Organisation\View\Helper\CreateOrganisationFromArray;
use Organisation\View\Helper\ViewHelperInitializer;

$config = [
    'controllers'     => [
        'initializers' => [
            ControllerInitializer::class
        ],
        'invokables'   => [
            OrganisationController::class        => OrganisationController::class,
            OrganisationManagerController::class => OrganisationManagerController::class,
            OrganisationAdminController::class   => OrganisationAdminController::class,
            JsonController::class                => JsonController::class,
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'    => [
        'initializers' => [
            ViewHelperInitializer::class
        ],
        'invokables'   => [
            'createLogoFromArray'         => CreateLogoFromArray::class,
            'createOrganisationFromArray' => CreateOrganisationFromArray::class,
            'organisationHandler'         => 'Organisation\View\Helper\OrganisationHandler',
            'organisationServiceProxy'    => 'Organisation\View\Helper\OrganisationServiceProxy',
            'organisationLink'            => 'Organisation\View\Helper\OrganisationLink',
            'organisationLogo'            => 'Organisation\View\Helper\OrganisationLogo',
        ]
    ],
    'service_manager' => [
        'initializers' => [
            ServiceInitializer::class
        ],
        'factories'    => [
            'organisation_module_config'  => 'Organisation\Service\ConfigServiceFactory',
            'organisation_module_options' => 'Organisation\Service\OptionServiceFactory',
            'organisation_cache'          => 'Organisation\Service\CacheFactory',
        ],
        'invokables'   => [
            OrganisationAssertion::class => OrganisationAssertion::class,
            OrganisationService::class   => OrganisationService::class,
            FormService::class           => FormService::class
        ]
    ],
    'doctrine'        => [
        'driver'       => [
            'organisation_annotation_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => [__DIR__ . '/../src/Organisation/Entity/']
            ],
            'orm_default'                    => [
                'drivers' => [
                    'Organisation\Entity' => 'organisation_annotation_driver',
                ]
            ]
        ],
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                ]
            ],
        ],
    ]
];
$configFiles = [
    __DIR__ . '/module.config.routes.php',
    __DIR__ . '/module.config.navigation.php',
    __DIR__ . '/module.config.authorize.php',
    __DIR__ . '/module.config.organisation.php',
    __DIR__ . '/module.option.organisation.php'
];
foreach ($configFiles as $configFile) {
    $config = Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
}
return $config;
