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
use Organisation\Form;
use Organisation\InputFilter;
use Organisation\Options;
use Organisation\Service;
use Organisation\View;

$config = [
    'controllers'     => [
        'abstract_factories' => [
            Controller\Factory\ControllerInvokableAbstractFactory::class
        ]
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'    => [
        'aliases'    => [
            'createLogoFromArray'         => View\Helper\CreateLogoFromArray::class,
            'createOrganisationFromArray' => View\Helper\CreateOrganisationFromArray::class,
            'organisationHandler'         => View\Helper\OrganisationHandler::class,
            'organisationServiceProxy'    => View\Helper\OrganisationServiceProxy::class,
            'organisationLink'            => View\Helper\OrganisationLink::class,
            'organisationLogo'            => View\Helper\OrganisationLogo::class,
        ],
        'invokables' => [
            'organisationformelement' => Form\View\Helper\OrganisationFormElement::class,
        ],
        'factories'  => [
            View\Helper\CreateLogoFromArray::class         => View\Factory\LinkInvokableFactory::class,
            View\Helper\CreateOrganisationFromArray::class => View\Factory\LinkInvokableFactory::class,
            View\Helper\OrganisationHandler::class         => View\Factory\LinkInvokableFactory::class,
            View\Helper\OrganisationServiceProxy::class    => View\Factory\LinkInvokableFactory::class,
            View\Helper\OrganisationLink::class            => View\Factory\LinkInvokableFactory::class,
            View\Helper\OrganisationLogo::class            => View\Factory\LinkInvokableFactory::class,
        ],
    ],
    'form_elements'   => [
        'aliases'   => [
            'Organisation' => Form\Element\Organisation::class,
        ],
        'factories' => [
            Form\Element\Organisation::class => \Zend\Form\ElementFactory::class,
        ],
    ],
    'service_manager' => [
        'factories'          => [
            Options\ModuleOptions::class       => Factory\ModuleOptionsFactory::class,
            Service\OrganisationService::class => Factory\OrganisationServiceFactory::class,
            Service\FormService::class         => Factory\FormServiceFactory::class,
            Form\Financial::class              => Factory\FormFactory::class,
            InputFilter\FinancialFilter::class => Factory\InputFilterFactory::class,
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
                'paths' => [__DIR__ . '/../src/Entity/'],
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
