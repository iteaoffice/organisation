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
use Affiliation\Service\DoaService;
use Affiliation\Service\LoiService;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use General\Service\CountryService;
use General\Service\GeneralService;
use Invoice\Search\Service\InvoiceSearchService;
use Invoice\Service\InvoiceService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Organisation\Controller;
use Organisation\Service;
use Organisation\Service\FormService;
use Program\Service\ProgramService;
use Project\Service\ProjectService;

return [
    ConfigAbstractFactory::class => [
        Controller\JsonController::class                   => [
            Service\OrganisationService::class,
            Service\ParentService::class,
            TranslatorInterface::class
        ],
        Controller\Organisation\NoteController::class      => [
            Service\OrganisationService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\ImageController::class                  => [
            Service\OrganisationService::class
        ],
        Controller\BoardController::class                  => [
            Service\BoardService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\Organisation\DetailsController::class   => [
            Service\OrganisationService::class,
            InvoiceSearchService::class,
            ProjectService::class,
            AffiliationService::class,
            DoaService::class,
            LoiService::class,
            EntityManager::class
        ],
        Controller\Organisation\FinancialController::class => [
            Service\OrganisationService::class,
            Service\FormService::class,
            GeneralService::class,
            TranslatorInterface::class
        ],
        Controller\Organisation\ListController::class      => [
            Service\OrganisationService::class,
            Search\Service\OrganisationSearchService::class
        ],
        Controller\Organisation\ManagerController::class   => [
            Service\OrganisationService::class,
            ProjectService::class,
            ContactService::class,
            AffiliationService::class,
            GeneralService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\Organisation\TypeController::class      => [
            Service\OrganisationService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\SelectionController::class              => [
            Service\SelectionService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\Parent\ContributionController::class    => [
            Service\ParentService::class,
            ProgramService::class,
            InvoiceService::class,
        ],
        Controller\Parent\ManagerController::class         => [
            Service\ParentService::class,
            Service\OrganisationService::class,
            ContactService::class,
            FormService::class,
            TranslatorInterface::class
        ],
        Controller\Parent\ListController::class            => [
            Service\ParentService::class,
            Service\OrganisationService::class,
            ContactService::class,
            EntityManager::class,
            TranslatorInterface::class
        ],
        Controller\Parent\DetailsController::class         => [
            Service\ParentService::class,
            Service\OrganisationService::class,
            ContactService::class,
            ProgramService::class,
            InvoiceService::class,
            EntityManager::class,
            TranslatorInterface::class
        ],
        Controller\Parent\OrganisationController::class    => [
            Service\ParentService::class,
            ProjectService::class,
            AffiliationService::class,
            ContactService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\Parent\TypeController::class            => [
            Service\ParentService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\Parent\DoaController::class             => [
            Service\ParentService::class,
            EntityManager::class,
            GeneralService::class,
            ContactService::class,
            ProgramService::class,
            TranslatorInterface::class
        ],
        Controller\Parent\FinancialController::class       => [
            Service\ParentService::class,
            ContactService::class,
            ProjectService::class,
            CountryService::class,
            Service\OrganisationService::class,
            TranslatorInterface::class
        ],
        Controller\UpdateController::class                 => [
            Service\OrganisationService::class,
            GeneralService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\Update\ManagerController::class         => [
            Service\UpdateService::class,
            Service\OrganisationService::class,
            GeneralService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ]
    ]
];
