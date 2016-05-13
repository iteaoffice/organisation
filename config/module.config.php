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
use Organisation\Navigation;
use Organisation\Options;
use Organisation\Service;
use Organisation\View;
use Zend\Stdlib;

$config = [
    'controllers'        => [
        'factories' => [
            Controller\JsonController::class                  => Controller\Factory\ControllerFactory::class,
            Controller\OrganisationAdminController::class     => Controller\Factory\ControllerFactory::class,
            Controller\OrganisationController::class          => Controller\Factory\ControllerFactory::class,
            Controller\OrganisationFinancialController::class => Controller\Factory\ControllerFactory::class,
            Controller\OrganisationTypeController::class      => Controller\Factory\ControllerFactory::class,
        ]
    ],
    'controller_plugins' => [
        'aliases'   => [
            'getOrganisationFilter' => Controller\Plugin\GetFilter::class,
        ],
        'factories' => [
            Controller\Plugin\GetFilter::class => Controller\Factory\PluginFactory::class,
        ]
    ],
    'view_manager'       => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'       => [
        'aliases'    => [
            'organisationHandler'  => View\Helper\OrganisationHandler::class,
            'organisationLink'     => View\Helper\OrganisationLink::class,
            'organisationTypeLink' => View\Helper\TypeLink::class,
            'organisationLogo'     => View\Helper\OrganisationLogo::class,
        ],
        'invokables' => [
            'organisationformelement' => Form\View\Helper\OrganisationFormElement::class,
        ],
        'factories'  => [
            View\Helper\OrganisationHandler::class => View\Factory\ViewHelperFactory::class,
            View\Helper\OrganisationLink::class    => View\Factory\ViewHelperFactory::class,
            View\Helper\TypeLink::class            => View\Factory\ViewHelperFactory::class,
            View\Helper\OrganisationLogo::class    => View\Factory\ViewHelperFactory::class,
        ],
    ],
    'form_elements'      => [
        'aliases'   => [
            'Organisation' => Form\Element\Organisation::class,
        ],
        'factories' => [
            Form\Element\Organisation::class => \Zend\Form\ElementFactory::class,
        ],
    ],
    'service_manager'    => [
        'factories' => [
            Options\ModuleOptions::class                  => Factory\ModuleOptionsFactory::class,
            Service\OrganisationService::class            => Factory\OrganisationServiceFactory::class,
            Service\FormService::class                    => Factory\FormServiceFactory::class,
            Form\FinancialForm::class                     => Factory\FormFactory::class,
            Form\OrganisationForm::class                  => Factory\FormFactory::class,
            InputFilter\FinancialFilter::class            => Factory\InputFilterFactory::class,
            Acl\Assertion\Organisation::class             => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Type::class                     => Acl\Factory\AssertionFactory::class,
            Navigation\Invokable\OrganisationLabel::class => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\TypeLabel::class         => Navigation\Factory\NavigationInvokableFactory::class,

        ],
    ],
    'doctrine'           => [
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
foreach (Stdlib\Glob::glob(__DIR__ . '/module.config.{,*}.php', Stdlib\Glob::GLOB_BRACE) as $file) {
    $config = Stdlib\ArrayUtils::merge($config, include $file);
}
return $config;
