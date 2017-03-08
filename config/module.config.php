<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
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
            Controller\NoteController::class                  => Controller\Factory\ControllerFactory::class,
            Controller\OrganisationAdminController::class     => Controller\Factory\ControllerFactory::class,
            Controller\OrganisationController::class          => Controller\Factory\ControllerFactory::class,
            Controller\OrganisationFinancialController::class => Controller\Factory\ControllerFactory::class,
            Controller\OrganisationTypeController::class      => Controller\Factory\ControllerFactory::class,
            Controller\ParentController::class                => Controller\Factory\ControllerFactory::class,
            Controller\ParentOrganisationController::class    => Controller\Factory\ControllerFactory::class,
            Controller\ParentTypeController::class            => Controller\Factory\ControllerFactory::class,
            Controller\ParentDoaController::class             => Controller\Factory\ControllerFactory::class,
            Controller\ParentStatusController::class          => Controller\Factory\ControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases'   => [
            'getOrganisationFilter'                        => Controller\Plugin\GetFilter::class,
            'handleParentAndProjectImport'                 => Controller\Plugin\HandleParentAndProjectImport::class,
            'handleParentImport'                           => Controller\Plugin\HandleParentImport::class,
            'renderOverviewExtraVariableContributionSheet' => Controller\Plugin\RenderOverviewExtraVariableContributionSheet::class,
            'renderOverviewVariableContributionSheet'      => Controller\Plugin\RenderOverviewVariableContributionSheet::class,
            'mergeOrganisation'                            => Controller\Plugin\MergeOrganisation::class,

        ],
        'factories' => [
            Controller\Plugin\GetFilter::class                                    => Controller\Factory\PluginFactory::class,
            Controller\Plugin\HandleParentAndProjectImport::class                 => Controller\Factory\PluginFactory::class,
            Controller\Plugin\HandleParentImport::class                           => Controller\Factory\PluginFactory::class,
            Controller\Plugin\RenderOverviewExtraVariableContributionSheet::class => Controller\Factory\PluginFactory::class,
            Controller\Plugin\RenderOverviewVariableContributionSheet::class      => Controller\Factory\PluginFactory::class,
            Controller\Plugin\MergeOrganisation::class                            => Controller\Factory\PluginFactory::class,
        ],
    ],
    'view_manager'       => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'       => [
        'aliases'    => [
            'organisationHandler'               => View\Helper\OrganisationHandler::class,
            'organisationLink'                  => View\Helper\OrganisationLink::class,
            'organisationTypeLink'              => View\Helper\TypeLink::class,
            'organisationLogo'                  => View\Helper\OrganisationLogo::class,
            'organisationNoteLink'              => View\Helper\NoteLink::class,
            'parentLink'                        => View\Helper\ParentLink::class,
            'parentOrganisationLink'            => View\Helper\ParentOrganisationLink::class,
            'parentStatusLink'                  => View\Helper\ParentStatusLink::class,
            'parentDoaLink'                     => View\Helper\ParentDoaLink::class,
            'parentTypeLink'                    => View\Helper\ParentTypeLink::class,
            'overviewVariableContribution'      => View\Helper\OverviewVariableContribution::class,
            'overviewExtraVariableContribution' => View\Helper\OverviewExtraVariableContribution::class,
        ],
        'invokables' => [
            'organisationformelement' => Form\View\Helper\OrganisationFormElement::class,
        ],
        'factories'  => [
            View\Helper\OrganisationHandler::class               => View\Factory\ViewHelperFactory::class,
            View\Helper\OrganisationLink::class                  => View\Factory\ViewHelperFactory::class,
            View\Helper\TypeLink::class                          => View\Factory\ViewHelperFactory::class,
            View\Helper\NoteLink::class                          => View\Factory\ViewHelperFactory::class,
            View\Helper\OrganisationLogo::class                  => View\Factory\ViewHelperFactory::class,
            View\Helper\ParentLink::class                        => View\Factory\ViewHelperFactory::class,
            View\Helper\ParentTypeLink::class                    => View\Factory\ViewHelperFactory::class,
            View\Helper\ParentStatusLink::class                  => View\Factory\ViewHelperFactory::class,
            View\Helper\ParentDoaLink::class                     => View\Factory\ViewHelperFactory::class,
            View\Helper\ParentOrganisationLink::class            => View\Factory\ViewHelperFactory::class,
            View\Helper\OverviewVariableContribution::class      => View\Factory\ViewHelperFactory::class,
            View\Helper\OverviewExtraVariableContribution::class => View\Factory\ViewHelperFactory::class,
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
            Options\ModuleOptions::class                         => Factory\ModuleOptionsFactory::class,
            Service\OrganisationService::class                   => Factory\OrganisationServiceFactory::class,
            Service\ParentService::class                         => Factory\OrganisationServiceFactory::class,
            Service\FormService::class                           => Factory\FormServiceFactory::class,
            Form\FinancialForm::class                            => Factory\FormFactory::class,
            Form\OrganisationForm::class                         => Factory\FormFactory::class,
            InputFilter\FinancialFilter::class                   => Factory\InputFilterFactory::class,
            InputFilter\OrganisationFilter::class                => Factory\InputFilterFactory::class,
            InputFilter\OParentFilter::class                     => Factory\InputFilterFactory::class,
            InputFilter\Parent\TypeFilter::class                 => Factory\InputFilterFactory::class,
            InputFilter\Parent\StatusFilter::class               => Factory\InputFilterFactory::class,
            Acl\Assertion\Organisation::class                    => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Type::class                            => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Note::class                            => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\OParent::class                         => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Parent\Doa::class                      => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Parent\Type::class                     => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Parent\Status::class                   => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Parent\Organisation::class             => Acl\Factory\AssertionFactory::class,
            Navigation\Invokable\OrganisationLabel::class        => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\TypeLabel::class                => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\ParentLabel::class              => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\Parent\OrganisationLabel::class => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\Parent\TypeLabel::class         => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\Parent\DoaLabel::class          => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\Parent\StatusLabel::class       => Navigation\Factory\NavigationInvokableFactory::class,

        ],
    ],
    'doctrine'           => [
        'driver' => [
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
    ],
];
foreach (Stdlib\Glob::glob(__DIR__ . '/module.config.{,*}.php', Stdlib\Glob::GLOB_BRACE) as $file) {
    $config = Stdlib\ArrayUtils::merge($config, include $file);
}
return $config;
