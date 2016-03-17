<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
use Organisation\Acl;
use Organisation\Controller;
use Organisation\Factory;
use Organisation\Form\View\Helper\OrganisationFormElement;
use Organisation\Options;
use Organisation\Service;
use Organisation\View\Helper;

$config = [
    'controllers'     => [
        'invokables'         => [
            //Controller\OrganisationController::class          ,
            //Controller\OrganisationManagerController::class   ,
            //Controller\OrganisationAdminController::class     ,
            //Controller\OrganisationFinancialController::class ,
            //Controller\JsonController::class                  ,
        ],
        'abstract_factories' => [
            Controller\Factory\ControllerInvokableAbstractFactory::class
        ]
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'    => [
        'invokables' => [
            'organisationformelement'     => OrganisationFormElement::class,
            'createLogoFromArray'         => Helper\CreateLogoFromArray::class,
            'createOrganisationFromArray' => Helper\CreateOrganisationFromArray::class,
            'organisationHandler'         => Helper\OrganisationHandler::class,
            'organisationServiceProxy'    => Helper\OrganisationServiceProxy::class,
            'organisationLink'            => Helper\OrganisationLink::class,
            'organisationLogo'            => Helper\OrganisationLogo::class,
        ],
    ],
    'service_manager' => [
        'factories'          => [
            Options\ModuleOptions::class       => Factory\ModuleOptionsFactory::class,
            Service\OrganisationService::class => Factory\OrganisationServiceFactory::class,
            Service\FormService::class         => Factory\FormServiceFactory::class,
            //Acl\Assertion\Organisation::class
        ],
        'abstract_factories' => [
            Acl\Factory\AssertionInvokableAbstractFactory::class
        ],
        'invokables'         => [
            'organisation_organisation_form_filter' => 'Organisation\Form\FilterOrganisation',
            'organisation_financial_form_filter'    => 'Organisation\Form\FilterOrganisation',
        ],
    ],
    'doctrine'        => [
        'driver'       => [
            'organisation_annotation_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => [__DIR__ . '/../src/Organisation/Entity/'],
            ],
            'orm_default'                    => [
                'drivers' => [
                    'Organisation\Entity' => 'organisation_annotation_driver',
                ],
            ],
        ],
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                ],
            ],
        ],
    ],
];
$configFiles = [
    __DIR__ . '/module.config.routes.php',
    __DIR__ . '/module.config.navigation.php',
    __DIR__ . '/module.config.authorize.php',
    __DIR__ . '/module.option.organisation.php',
];
foreach ($configFiles as $configFile) {
    $config = Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
}
return $config;
