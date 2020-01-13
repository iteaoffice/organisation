<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
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
use Doctrine\ORM\EntityManager;
use ErrorHeroModule\Handler\Logging;
use General\Service\CountryService;
use General\Service\GeneralService;
use Invoice\Search\Service\InvoiceSearchService;
use Invoice\Service\InvoiceService;
use Organisation\Controller;
use Organisation\Service;
use Program\Service\ProgramService;
use Project\Service\ProjectService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Controller\ConsoleController::class               => [
            Service\OrganisationService::class
        ],
        Controller\JsonController::class                  => [
            Service\OrganisationService::class,
            Service\ParentService::class,
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
            Search\Service\OrganisationSearchService::class,
            InvoiceService::class,
            InvoiceSearchService::class,
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
            ProgramService::class,
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
        Controller\UpdateController::class                => [
            Service\OrganisationService::class,
            GeneralService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\UpdateManagerController::class         => [
            Service\UpdateService::class,
            GeneralService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ]
    ]
];
