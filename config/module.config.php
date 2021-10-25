<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Organisation;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use General\Navigation\Factory\NavigationInvokableFactory;
use General\View\Factory\ImageHelperFactory;
use General\View\Factory\LinkHelperFactory;
use Laminas\Form\ElementFactory;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib;

$config = [
    'controllers'        => [
        'factories'  => [
            Controller\JsonController::class => ConfigAbstractFactory::class,

            Controller\ImageController::class => ConfigAbstractFactory::class,
            Controller\BoardController::class => ConfigAbstractFactory::class,

            Controller\AdvisoryBoard\City\ManagerController::class             => ConfigAbstractFactory::class,
            Controller\AdvisoryBoard\City\Manager\DetailsController::class     => ConfigAbstractFactory::class,
            Controller\AdvisoryBoard\Solution\ManagerController::class         => ConfigAbstractFactory::class,
            Controller\AdvisoryBoard\Solution\Manager\DetailsController::class => ConfigAbstractFactory::class,

            Controller\Organisation\DetailsController::class   => ConfigAbstractFactory::class,
            Controller\Organisation\FinancialController::class => ConfigAbstractFactory::class,
            Controller\Organisation\ListController::class      => ConfigAbstractFactory::class,
            Controller\Organisation\NoteController::class      => ConfigAbstractFactory::class,
            Controller\Organisation\ManagerController::class   => ConfigAbstractFactory::class,
            Controller\Organisation\TypeController::class      => ConfigAbstractFactory::class,

            Controller\Parent\ContributionController::class => ConfigAbstractFactory::class,
            Controller\Parent\DetailsController::class      => ConfigAbstractFactory::class,
            Controller\Parent\DoaController::class          => ConfigAbstractFactory::class,
            Controller\Parent\FinancialController::class    => ConfigAbstractFactory::class,
            Controller\Parent\ListController::class         => ConfigAbstractFactory::class,
            Controller\Parent\ManagerController::class      => ConfigAbstractFactory::class,
            Controller\Parent\OrganisationController::class => ConfigAbstractFactory::class,

            Controller\Parent\TypeController::class => ConfigAbstractFactory::class,

            Controller\SelectionController::class      => ConfigAbstractFactory::class,
            Controller\UpdateController::class         => ConfigAbstractFactory::class,
            Controller\Update\ManagerController::class => ConfigAbstractFactory::class,
        ],
        'invokables' => [
            Controller\Parent\ImportController::class
        ]
    ],
    'controller_plugins' => [
        'aliases'   => [
            'getOrganisationFilter'                        => Controller\Plugin\GetFilter::class,
            'handleParentAndProjectImport'                 => Controller\Plugin\HandleParentAndProjectImport::class,
            'renderOverviewExtraVariableContributionSheet' => Controller\Plugin\RenderOverviewExtraVariableContributionSheet::class,
            'renderOverviewVariableContributionSheet'      => Controller\Plugin\RenderOverviewVariableContributionSheet::class,
            'organisationMerge'                            => Controller\Plugin\Merge\OrganisationMerge::class,
            'parentOrganisationMerge'                      => Controller\Plugin\Merge\ParentOrganisationMerge::class,
            'organisationSelectionExport'                  => Controller\Plugin\SelectionExport::class,
        ],
        'factories' => [
            Controller\Plugin\GetFilter::class                                    => Factory\InvokableFactory::class,
            Controller\Plugin\HandleParentAndProjectImport::class                 => ConfigAbstractFactory::class,
            Controller\Plugin\RenderOverviewExtraVariableContributionSheet::class => ConfigAbstractFactory::class,
            Controller\Plugin\RenderOverviewVariableContributionSheet::class      => ConfigAbstractFactory::class,
            Controller\Plugin\Merge\OrganisationMerge::class                      => ConfigAbstractFactory::class,
            Controller\Plugin\Merge\ParentOrganisationMerge::class                => ConfigAbstractFactory::class,
            Controller\Plugin\SelectionExport::class                              => ConfigAbstractFactory::class,
        ],
    ],
    'view_manager'       => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'       => [
        'aliases'    => [
            'organisationLink'          => View\Helper\OrganisationLink::class,
            'organisationTypeLink'      => View\Helper\Organisation\TypeLink::class,
            'organisationLogo'          => View\Helper\OrganisationLogo::class,
            'organisationNoteLink'      => View\Helper\NoteLink::class,
            'boardLink'                 => View\Helper\BoardLink::class,
            'organisationSelectionLink' => View\Helper\SelectionLink::class,
            'parentLink'                => View\Helper\Parent\ParentLink::class,
            'parentOrganisationLink'    => View\Helper\Parent\OrganisationLink::class,
            'parentDoaLink'             => View\Helper\Parent\DoaLink::class,
            'parentTypeLink'            => View\Helper\Parent\TypeLink::class,
            'parentFinancialLink'       => View\Helper\Parent\FinancialLink::class,

            'advisoryBoardCityLink'  => View\Helper\AdvisoryBoard\CityLink::class,
            'advisoryBoardCityImage' => View\Helper\AdvisoryBoard\CityImage::class,

            'advisoryBoardSolutionLink'  => View\Helper\AdvisoryBoard\SolutionLink::class,
            'advisoryBoardSolutionImage' => View\Helper\AdvisoryBoard\SolutionImage::class,

            'organisationUpdateLink'            => View\Helper\UpdateLink::class,
            'organisationUpdateLogo'            => View\Helper\UpdateLogo::class,
            'organisationUpdateNotification'    => View\Helper\UpdateNotification::class,
            'overviewVariableContribution'      => View\Helper\Parent\Contribution\OverviewVariableContribution::class,
            'overviewExtraVariableContribution' => View\Helper\Parent\Contribution\OverviewExtraVariableContribution::class,
            'organisationformelement'           => Form\View\Helper\OrganisationFormElement::class,
            'parentformelement'                 => Form\View\Helper\ParentFormElement::class,

            'organisationselect' => Form\View\Helper\OrganisationSelect::class,
        ],
        'invokables' => [
            Form\View\Helper\OrganisationSelect::class
        ],
        'factories'  => [
            View\Handler\OrganisationHandler::class                                  => ConfigAbstractFactory::class,
            View\Handler\AdvisoryBoard\CityHandler::class                            => ConfigAbstractFactory::class,
            View\Handler\AdvisoryBoard\SolutionHandler::class                        => ConfigAbstractFactory::class,
            View\Helper\OrganisationLink::class                                      => LinkHelperFactory::class,
            View\Helper\Organisation\TypeLink::class                                 => LinkHelperFactory::class,
            View\Helper\NoteLink::class                                              => LinkHelperFactory::class,
            View\Helper\BoardLink::class                                             => LinkHelperFactory::class,
            View\Helper\SelectionLink::class                                         => LinkHelperFactory::class,
            View\Helper\OrganisationLogo::class                                      => ImageHelperFactory::class,
            View\Helper\Parent\ParentLink::class                                     => LinkHelperFactory::class,
            View\Helper\Parent\TypeLink::class                                       => LinkHelperFactory::class,
            View\Helper\Parent\DoaLink::class                                        => LinkHelperFactory::class,
            View\Helper\Parent\FinancialLink::class                                  => LinkHelperFactory::class,
            View\Helper\Parent\OrganisationLink::class                               => LinkHelperFactory::class,
            View\Helper\AdvisoryBoard\CityLink::class                                => LinkHelperFactory::class,
            View\Helper\AdvisoryBoard\CityImage::class                               => ImageHelperFactory::class,
            View\Helper\AdvisoryBoard\SolutionLink::class                            => LinkHelperFactory::class,
            View\Helper\AdvisoryBoard\SolutionImage::class                           => ImageHelperFactory::class,
            View\Helper\UpdateLink::class                                            => LinkHelperFactory::class,
            View\Helper\UpdateLogo::class                                            => ImageHelperFactory::class,
            View\Helper\UpdateNotification::class                                    => ConfigAbstractFactory::class,
            View\Helper\Parent\Contribution\OverviewVariableContribution::class      => ConfigAbstractFactory::class,
            View\Helper\Parent\Contribution\OverviewExtraVariableContribution::class => ConfigAbstractFactory::class,
            Form\View\Helper\OrganisationFormElement::class                          => ConfigAbstractFactory::class,
            Form\View\Helper\ParentFormElement::class                                => ConfigAbstractFactory::class
        ],
    ],
    'form_elements'      => [
        'aliases'   => [
            'OrganisationElement' => Form\Element\OrganisationElement::class,
            'ParentElement'       => Form\Element\ParentElement::class,
        ],
        'factories' => [
            Form\Element\OrganisationElement::class => ElementFactory::class,
            Form\Element\ParentElement::class       => ElementFactory::class,
        ],
    ],
    'service_manager'    => [
        'factories'  => [
            Command\Cleanup::class                 => ConfigAbstractFactory::class,
            Options\ModuleOptions::class           => Factory\ModuleOptionsFactory::class,
            Form\OrganisationForm::class           => ConfigAbstractFactory::class,
            Form\AdvisoryBoard\CityForm::class     => ConfigAbstractFactory::class,
            Form\AdvisoryBoard\SolutionForm::class => ConfigAbstractFactory::class,
            Form\UpdateForm::class                 => ConfigAbstractFactory::class,
            Form\FinancialForm::class              => ConfigAbstractFactory::class,
            Service\OrganisationService::class     => Factory\InvokableFactory::class,
            Service\UpdateService::class           => ConfigAbstractFactory::class,
            Service\SelectionService::class        => ConfigAbstractFactory::class,
            Service\BoardService::class            => ConfigAbstractFactory::class,

            Service\AdvisoryBoard\CityService::class     => ConfigAbstractFactory::class,
            Service\AdvisoryBoard\SolutionService::class => ConfigAbstractFactory::class,

            Service\ParentService::class                              => Factory\InvokableFactory::class,
            Service\FormService::class                                => Factory\FormServiceFactory::class,
            InputFilter\FinancialFilter::class                        => Factory\InputFilterFactory::class,
            InputFilter\BoardFilter::class                            => Factory\InputFilterFactory::class,
            InputFilter\OrganisationFilter::class                     => Factory\InputFilterFactory::class,
            InputFilter\SelectionFilter::class                        => Factory\InputFilterFactory::class,
            InputFilter\ParentEntityFilter::class                     => Factory\InputFilterFactory::class,
            InputFilter\Parent\TypeFilter::class                      => Factory\InputFilterFactory::class,
            InputFilter\AdvisoryBoard\CityFilter::class               => Factory\InputFilterFactory::class,
            InputFilter\AdvisoryBoard\SolutionFilter::class           => Factory\InputFilterFactory::class,
            Acl\Assertion\OrganisationAssertion::class                => Factory\InvokableFactory::class,
            Acl\Assertion\TypeAssertion::class                        => Factory\InvokableFactory::class,
            Acl\Assertion\NoteAssertion::class                        => Factory\InvokableFactory::class,
            Acl\Assertion\ParentAssertion::class                      => Factory\InvokableFactory::class,
            Acl\Assertion\UpdateAssertion::class                      => Factory\InvokableFactory::class,
            Acl\Assertion\Parent\FinancialAssertion::class            => Factory\InvokableFactory::class,
            Acl\Assertion\Parent\DoaAssertion::class                  => Factory\InvokableFactory::class,
            Acl\Assertion\Parent\TypeAssertion::class                 => Factory\InvokableFactory::class,
            Acl\Assertion\Parent\OrganisationAssertion::class         => Factory\InvokableFactory::class,
            Acl\Assertion\AdvisoryBoard\CityAssertion::class          => Factory\InvokableFactory::class,
            Acl\Assertion\AdvisoryBoard\SolutionAssertion::class      => Factory\InvokableFactory::class,
            Search\Service\OrganisationSearchService::class           => ConfigAbstractFactory::class,
            Search\Service\AdvisoryBoard\CitySearchService::class     => ConfigAbstractFactory::class,
            Search\Service\AdvisoryBoard\SolutionSearchService::class => ConfigAbstractFactory::class,
            Navigation\Invokable\OrganisationLabel::class             => NavigationInvokableFactory::class,
            Navigation\Invokable\TypeLabel::class                     => NavigationInvokableFactory::class,
            Navigation\Invokable\SelectionLabel::class                => NavigationInvokableFactory::class,
            Navigation\Invokable\BoardLabel::class                    => NavigationInvokableFactory::class,
            Navigation\Invokable\ParentLabel::class                   => NavigationInvokableFactory::class,
            Navigation\Invokable\UpdateLabel::class                   => NavigationInvokableFactory::class,
            Navigation\Invokable\Parent\OrganisationLabel::class      => NavigationInvokableFactory::class,
            Navigation\Invokable\Parent\TypeLabel::class              => NavigationInvokableFactory::class,
            Navigation\Invokable\Parent\DoaLabel::class               => NavigationInvokableFactory::class,
            Navigation\Invokable\Parent\FinancialLabel::class         => NavigationInvokableFactory::class,
            Navigation\Invokable\AdvisoryBoard\CityLabel::class       => NavigationInvokableFactory::class,
            Navigation\Invokable\AdvisoryBoard\SolutionLabel::class   => NavigationInvokableFactory::class
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
