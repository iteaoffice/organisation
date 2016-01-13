<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
use Organisation\Acl\Assertion;
use Organisation\Controller;
use Organisation\Form\View\Helper\OrganisationFormElement;
use Organisation\Service;
use Organisation\View\Helper;

$config = [
    'controllers'     => [
        'initializers' => [
            Controller\ControllerInitializer::class,
        ],
        'invokables'   => [
            Controller\OrganisationController::class          => Controller\OrganisationController::class,
            Controller\OrganisationManagerController::class   => Controller\OrganisationManagerController::class,
            Controller\OrganisationAdminController::class     => Controller\OrganisationAdminController::class,
            Controller\OrganisationFinancialController::class => Controller\OrganisationFinancialController::class,
            Controller\JsonController::class                  => Controller\JsonController::class,
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'    => [
        'initializers' => [
            Helper\ViewHelperInitializer::class,
        ],
        'invokables'   => [
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
        'initializers' => [
            Service\ServiceInitializer::class,
        ],
        'factories'    => [
            'organisation_module_config'  => Service\ConfigServiceFactory::class,
            'organisation_module_options' => Service\OptionServiceFactory::class,
            'organisation_cache'          => Service\CacheFactory::class,
        ],
        'invokables'   => [
            Assertion\Organisation::class           => Assertion\Organisation::class,
            Service\OrganisationService::class      => Service\OrganisationService::class,
            Service\FormService::class              => Service\FormService::class,
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
    __DIR__ . '/module.config.organisation.php',
    __DIR__ . '/module.option.organisation.php',
];
foreach ($configFiles as $configFile) {
    $config = Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
}
return $config;
