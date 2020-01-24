<?php

use Admin\Navigation\Factory\NavigationInvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use General\View\Factory\ImageHelperFactory;
use General\View\Factory\LinkHelperFactory;
use Laminas\Form\ElementFactory;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib;
use Organisation\Acl;
use Organisation\Controller;
use Organisation\Factory;
use Organisation\Form;
use Organisation\InputFilter;
use Organisation\Navigation;
use Organisation\Options;
use Organisation\Search;
use Organisation\Service;
use Organisation\View;

/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

$config = [
    'controllers'        => [
        'factories' => [
            Controller\JsonController::class                  => ConfigAbstractFactory::class,
            Controller\NoteController::class                  => ConfigAbstractFactory::class,
            Controller\ConsoleController::class               => ConfigAbstractFactory::class,
            Controller\ImageController::class                 => ConfigAbstractFactory::class,
            Controller\OrganisationAdminController::class     => ConfigAbstractFactory::class,
            Controller\OrganisationFinancialController::class => ConfigAbstractFactory::class,
            Controller\OrganisationTypeController::class      => ConfigAbstractFactory::class,
            Controller\ParentController::class                => ConfigAbstractFactory::class,
            Controller\ParentOrganisationController::class    => ConfigAbstractFactory::class,
            Controller\ParentTypeController::class            => ConfigAbstractFactory::class,
            Controller\ParentDoaController::class             => ConfigAbstractFactory::class,
            Controller\ParentFinancialController::class       => ConfigAbstractFactory::class,
            Controller\UpdateController::class                => ConfigAbstractFactory::class,
            Controller\UpdateManagerController::class         => ConfigAbstractFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases'   => [
            'getOrganisationFilter'                        => Controller\Plugin\GetFilter::class,
            'handleParentAndProjectImport'                 => Controller\Plugin\HandleParentAndProjectImport::class,
            'renderOverviewExtraVariableContributionSheet' => Controller\Plugin\RenderOverviewExtraVariableContributionSheet::class,
            'renderOverviewVariableContributionSheet'      => Controller\Plugin\RenderOverviewVariableContributionSheet::class,
            'mergeOrganisation'                            => Controller\Plugin\MergeOrganisation::class,
            'mergeParentOrganisation'                      => Controller\Plugin\MergeParentOrganisation::class,
        ],
        'factories' => [
            Controller\Plugin\GetFilter::class                                    => Factory\InvokableFactory::class,
            Controller\Plugin\HandleParentAndProjectImport::class                 => ConfigAbstractFactory::class,
            Controller\Plugin\RenderOverviewExtraVariableContributionSheet::class => ConfigAbstractFactory::class,
            Controller\Plugin\RenderOverviewVariableContributionSheet::class      => ConfigAbstractFactory::class,
            Controller\Plugin\MergeOrganisation::class                            => ConfigAbstractFactory::class,
            Controller\Plugin\MergeParentOrganisation::class                      => ConfigAbstractFactory::class,
        ],
    ],
    'view_manager'       => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'       => [
        'aliases'    => [
            'organisationLink'                  => View\Helper\OrganisationLink::class,
            'organisationTypeLink'              => View\Helper\TypeLink::class,
            'organisationLogo'                  => View\Helper\OrganisationLogo::class,
            'organisationNoteLink'              => View\Helper\NoteLink::class,
            'parentLink'                        => View\Helper\Parent\ParentLink::class,
            'parentOrganisationLink'            => View\Helper\Parent\OrganisationLink::class,
            'parentDoaLink'                     => View\Helper\Parent\DoaLink::class,
            'parentTypeLink'                    => View\Helper\Parent\TypeLink::class,
            'parentFinancialLink'               => View\Helper\Parent\FinancialLink::class,
            'organisationUpdateLink'            => View\Helper\UpdateLink::class,
            'organisationUpdateLogo'            => View\Helper\UpdateLogo::class,
            'organisationUpdateNotification'    => View\Helper\UpdateNotification::class,
            'overviewVariableContribution'      => View\Helper\OverviewVariableContribution::class,
            'overviewExtraVariableContribution' => View\Helper\OverviewExtraVariableContribution::class,
            'organisationformelement'           => Form\View\Helper\OrganisationFormElement::class,
            'parentformelement'                 => Form\View\Helper\ParentFormElement::class,

            'organisationselect' => Form\View\Helper\OrganisationSelect::class,
        ],
        'invokables' => [
            Form\View\Helper\OrganisationSelect::class
        ],
        'factories'  => [
            View\Handler\OrganisationHandler::class              => ConfigAbstractFactory::class,
            View\Helper\OrganisationLink::class                  => LinkHelperFactory::class,
            View\Helper\TypeLink::class                          => LinkHelperFactory::class,
            View\Helper\NoteLink::class                          => LinkHelperFactory::class,
            View\Helper\OrganisationLogo::class                  => ImageHelperFactory::class,
            View\Helper\Parent\ParentLink::class                 => LinkHelperFactory::class,
            View\Helper\Parent\TypeLink::class                   => LinkHelperFactory::class,
            View\Helper\Parent\DoaLink::class                    => LinkHelperFactory::class,
            View\Helper\Parent\FinancialLink::class              => LinkHelperFactory::class,
            View\Helper\Parent\OrganisationLink::class           => LinkHelperFactory::class,
            View\Helper\UpdateLink::class                        => LinkHelperFactory::class,
            View\Helper\UpdateLogo::class                        => ImageHelperFactory::class,
            View\Helper\UpdateNotification::class                => ConfigAbstractFactory::class,
            View\Helper\OverviewVariableContribution::class      => ConfigAbstractFactory::class,
            View\Helper\OverviewExtraVariableContribution::class => ConfigAbstractFactory::class,
            Form\View\Helper\OrganisationFormElement::class      => ConfigAbstractFactory::class,
            Form\View\Helper\ParentFormElement::class            => ConfigAbstractFactory::class
        ],
    ],
    'form_elements'      => [
        'aliases'   => [
            'Organisation' => Form\Element\Organisation::class,
            'OParent'      => Form\Element\OParent::class,
        ],
        'factories' => [
            Form\Element\Organisation::class => ElementFactory::class,
            Form\Element\OParent::class      => ElementFactory::class,
        ],
    ],
    'service_manager'    => [
        'factories'  => [
            Options\ModuleOptions::class                         => Factory\ModuleOptionsFactory::class,
            Form\OrganisationForm::class                         => ConfigAbstractFactory::class,
            Form\UpdateForm::class                               => ConfigAbstractFactory::class,
            Form\FinancialForm::class                            => ConfigAbstractFactory::class,
            Service\OrganisationService::class                   => Factory\InvokableFactory::class,
            Service\UpdateService::class                         => ConfigAbstractFactory::class,
            Service\ParentService::class                         => Factory\InvokableFactory::class,
            Service\FormService::class                           => Factory\FormServiceFactory::class,
            InputFilter\FinancialFilter::class                   => Factory\InputFilterFactory::class,
            InputFilter\OrganisationFilter::class                => Factory\InputFilterFactory::class,
            InputFilter\OParentFilter::class                     => Factory\InputFilterFactory::class,
            InputFilter\Parent\TypeFilter::class                 => Factory\InputFilterFactory::class,
            Acl\Assertion\Organisation::class                    => Factory\InvokableFactory::class,
            Acl\Assertion\Type::class                            => Factory\InvokableFactory::class,
            Acl\Assertion\Note::class                            => Factory\InvokableFactory::class,
            Acl\Assertion\OParent::class                         => Factory\InvokableFactory::class,
            Acl\Assertion\UpdateAssertion::class                 => Factory\InvokableFactory::class,
            Acl\Assertion\Parent\Financial::class                => Factory\InvokableFactory::class,
            Acl\Assertion\Parent\Doa::class                      => Factory\InvokableFactory::class,
            Acl\Assertion\Parent\Type::class                     => Factory\InvokableFactory::class,
            Acl\Assertion\Parent\Status::class                   => Factory\InvokableFactory::class,
            Acl\Assertion\Parent\Organisation::class             => Factory\InvokableFactory::class,
            Search\Service\OrganisationSearchService::class      => ConfigAbstractFactory::class,
            Navigation\Invokable\OrganisationLabel::class        => NavigationInvokableFactory::class,
            Navigation\Invokable\TypeLabel::class                => NavigationInvokableFactory::class,
            Navigation\Invokable\ParentLabel::class              => NavigationInvokableFactory::class,
            Navigation\Invokable\UpdateLabel::class              => NavigationInvokableFactory::class,
            Navigation\Invokable\Parent\OrganisationLabel::class => NavigationInvokableFactory::class,
            Navigation\Invokable\Parent\TypeLabel::class         => NavigationInvokableFactory::class,
            Navigation\Invokable\Parent\DoaLabel::class          => NavigationInvokableFactory::class,
            Navigation\Invokable\Parent\FinancialLabel::class    => NavigationInvokableFactory::class,
        ],
        'invokables' => [
            InputFilter\NoteFilter::class
        ]
    ],
    'doctrine'           => [
        'driver' => [
            'organisation_annotation_driver' => [
                'class' => AnnotationDriver::class,
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
