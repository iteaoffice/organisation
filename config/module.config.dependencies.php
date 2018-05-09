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

use Content\Navigation\Service\UpdateNavigationService;
use Content\Service\ArticleService;
use Organisation\Service\OrganisationService;
use Project\Options\ModuleOptions;
use Project\Service\ProjectService;
use Zend\Authentication\AuthenticationService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use ZfcTwig\View\TwigRenderer;

return [
    ConfigAbstractFactory::class => [

        View\Handler\OrganisationHandler::class => [
            'Application',
            'ViewHelperManager',
            TwigRenderer::class,
            AuthenticationService::class,
            UpdateNavigationService::class,
            TranslatorInterface::class,
            OrganisationService::class,
            ModuleOptions::class,
            ProjectService::class,
            ArticleService::class
        ],
    ]
];