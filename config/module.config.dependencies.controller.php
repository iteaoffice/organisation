<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2018 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation;

use Affiliation\Service\AffiliationService;
use Affiliation\Service\DoaService;
use Affiliation\Service\LoiService;
use Contact\Service\ContactService;
use Content\Service\ArticleService;
use Doctrine\ORM\EntityManager;
use ErrorHeroModule\Handler\Logging;
use General\Service\CountryService;
use General\Service\GeneralService;
use Invoice\Service\InvoiceService;
use Organisation\Controller;
use Organisation\Service;
use Program\Service\ProgramService;
use Project\Options\ModuleOptions;
use Project\Service\ProjectService;
use Zend\Authentication\AuthenticationService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use ZfcTwig\View\TwigRenderer;

return [
    ConfigAbstractFactory::class => [
        Controller\Plugin\MergeOrganisation::class        => [
            EntityManager::class,
            TranslatorInterface::class,
            Logging::class
        ],
        View\Handler\OrganisationHandler::class           => [
            'Application',
            'ViewHelperManager',
            TwigRenderer::class,
            AuthenticationService::class,
            TranslatorInterface::class,
            Service\OrganisationService::class,
            ModuleOptions::class,
            ProjectService::class,
            ArticleService::class
        ],
        Controller\JsonController::class                  => [
            Service\OrganisationService::class,
            TranslatorInterface::class
        ],
        Controller\NoteController::class                  => [
            Service\OrganisationService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\ImageController::class                 => [
            Service\OrganisationService::class
        ],
        Controller\OrganisationAdminController::class     => [
            Service\OrganisationService::class,
            InvoiceService::class,
            ProjectService::class,
            ContactService::class,
            AffiliationService::class,
            DoaService::class,
            LoiService::class,
            GeneralService::class,
            EntityManager::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\OrganisationFinancialController::class => [
            Service\OrganisationService::class,
            Service\FormService::class,
            GeneralService::class,
            TranslatorInterface::class
        ],
        Controller\OrganisationTypeController::class      => [
            Service\OrganisationService::class,
            Service\FormService::class
        ],
        Controller\ParentController::class                => [
            Service\ParentService::class,
            Service\OrganisationService::class,
            ContactService::class,
            ProgramService::class,
            InvoiceService::class,
            Service\FormService::class,
            EntityManager::class,
            TranslatorInterface::class
        ],
        Controller\ParentOrganisationController::class    => [
            Service\ParentService::class,
            ProjectService::class,
            AffiliationService::class,
            ContactService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\ParentTypeController::class            => [
            Service\ParentService::class,
            Service\FormService::class
        ],
        Controller\ParentDoaController::class             => [
            Service\ParentService::class,
            EntityManager::class,
            GeneralService::class,
            ContactService::class,
            TranslatorInterface::class
        ],
        Controller\ParentFinancialController::class       => [
            Service\ParentService::class,
            ContactService::class,
            ProjectService::class,
            CountryService::class,
            Service\OrganisationService::class,
            EntityManager::class,
            TranslatorInterface::class
        ],
    ]
];