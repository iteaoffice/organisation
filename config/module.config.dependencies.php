<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation;

use Affiliation\Service\AffiliationService;
use Contact\Service\ContactService;
use Content\Service\ArticleService;
use Doctrine\ORM\EntityManager;
use ErrorHeroModule\Handler\Logging;
use General\Service\CountryService;
use General\Service\EmailService;
use Invoice\Service\InvoiceService;
use Laminas\Authentication\AuthenticationService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Organisation\Controller;
use Organisation\Form\View\Helper\OrganisationFormElement;
use Organisation\Form\View\Helper\ParentFormElement;
use Organisation\Service;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use ZfcTwig\View\TwigRenderer;

return [
    ConfigAbstractFactory::class => [
        Search\Service\OrganisationSearchService::class                          => [
            'Config'
        ],
        View\Handler\OrganisationHandler::class                                  => [
            'Application',
            'ViewHelperManager',
            TwigRenderer::class,
            AuthenticationService::class,
            TranslatorInterface::class,
            Service\OrganisationService::class,
            Search\Service\OrganisationSearchService::class,
            ProjectService::class,
            ArticleService::class
        ],
        Controller\Plugin\HandleParentAndProjectImport::class                    => [
            EntityManager::class,
            CountryService::class,
            Service\ParentService::class,
            ProjectService::class,
            ContactService::class,
            Service\OrganisationService::class,
            CallService::class,
            ProgramService::class
        ],
        Controller\Plugin\RenderOverviewExtraVariableContributionSheet::class    => [
            Service\ParentService::class,
            Options\ModuleOptions::class,
            ProjectService::class,
            VersionService::class,
            ContactService::class,
            AffiliationService::class,
            TwigRenderer::class
        ],
        Controller\Plugin\RenderOverviewVariableContributionSheet::class         => [
            Service\ParentService::class,
            InvoiceService::class,
            Options\ModuleOptions::class,
            ProjectService::class,
            VersionService::class,
            ContactService::class,
            AffiliationService::class,
            TwigRenderer::class
        ],
        Controller\Plugin\Merge\OrganisationMerge::class                         => [
            EntityManager::class,
            Service\UpdateService::class,
            TranslatorInterface::class,
            Logging::class
        ],
        Controller\Plugin\Merge\ParentOrganisationMerge::class                   => [
            EntityManager::class,
            Logging::class
        ],
        Controller\Plugin\SelectionExport::class                                 => [
            Service\SelectionService::class,
            TranslatorInterface::class
        ],
        Form\OrganisationForm::class                                             => [
            EntityManager::class
        ],
        Form\UpdateForm::class                                                   => [
            EntityManager::class
        ],
        Form\FinancialForm::class                                                => [
            EntityManager::class
        ],
        OrganisationFormElement::class                                           => [
            Service\OrganisationService::class,
            'ViewHelperManager',
            TranslatorInterface::class
        ],
        ParentFormElement::class                                                 => [
            'ViewHelperManager',
            TranslatorInterface::class
        ],
        View\Helper\UpdateNotification::class                                    => [
            Service\UpdateService::class,
            TranslatorInterface::class
        ],
        View\Helper\Parent\Contribution\OverviewVariableContribution::class      => [
            TwigRenderer::class,
            ProjectService::class,
            VersionService::class,
            AffiliationService::class,
            InvoiceService::class,
            Service\ParentService::class,
            ContactService::class,
        ],
        View\Helper\Parent\Contribution\OverviewExtraVariableContribution::class => [
            TwigRenderer::class,
            ProjectService::class,
            VersionService::class,
            AffiliationService::class,
            InvoiceService::class,
            Service\ParentService::class,
            ContactService::class,
        ],
        Service\BoardService::class                                              => [
            EntityManager::class
        ],
        Service\SelectionService::class                                          => [
            EntityManager::class
        ],
        Service\UpdateService::class                                             => [
            EntityManager::class,
            Service\OrganisationService::class,
            EmailService::class
        ]
    ]
];
