<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace OrganisationTest\Service;

use Doctrine\ORM\EntityManager;
use Organisation\Entity\Organisation;
use Organisation\Search\Service\OrganisationSearchService;
use Organisation\Service\OrganisationService;
use Project\Entity\Project;
use Project\Entity\Result\Result;
use Testing\Util\AbstractServiceTest;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class OrganisationServiceTest
 *
 * @package OrganisationTest\Service
 */
class OrganisationServiceTest extends AbstractServiceTest
{
    /**
     *
     */
    public function testCanCreateService(): void
    {
        $container = new ServiceManager();
        $container->setService(EntityManager::class, $this->getEntityManagerMock());
        $container->setService(OrganisationSearchService::class, new OrganisationSearchService([]));
        $container->setService(TranslatorInterface::class, new Translator());
        $service = new OrganisationService($container);
        $this->assertInstanceOf(OrganisationService::class, $service);
    }

    public function testCanDeleteEmptyOrganisation(): void
    {
        $container = new ServiceManager();
        $container->setService(EntityManager::class, $this->getEntityManagerMock());
        $container->setService(OrganisationSearchService::class, new OrganisationSearchService([]));
        $container->setService(TranslatorInterface::class, new Translator());
        $service = new OrganisationService($container);
        $organisation = new Organisation();
        $this->assertTrue($service->canDeleteOrganisation($organisation));
    }

    public function testCanNotDeleteOrganisation(): void
    {
        $container = new ServiceManager();
        $container->setService(EntityManager::class, $this->getEntityManagerMock());
        $container->setService(OrganisationSearchService::class, new OrganisationSearchService([]));
        $container->setService(TranslatorInterface::class, new Translator());
        $service = new OrganisationService($container);
        $organisation = new Organisation();
        $organisation->getResult()->add(new Result());
        $this->assertFalse($service->canDeleteOrganisation($organisation));
    }

    public function testCanParseDebtorNumber(): void
    {
        $container = new ServiceManager();
        $container->setService(EntityManager::class, $this->getEntityManagerMock());
        $container->setService(OrganisationSearchService::class, new OrganisationSearchService([]));
        $container->setService(TranslatorInterface::class, new Translator());
        $service = new OrganisationService($container);
        $organisation = new Organisation();
        $organisation->setId(1);
        $this->assertNotNull($service->parseDebtorNumber($organisation));
    }

    public function testCanParseCreditNumber(): void
    {
        $container = new ServiceManager();
        $container->setService(EntityManager::class, $this->getEntityManagerMock());
        $container->setService(OrganisationSearchService::class, new OrganisationSearchService([]));
        $container->setService(TranslatorInterface::class, new Translator());
        $service = new OrganisationService($container);
        $organisation = new Organisation();
        $organisation->setId(1);
        $this->assertNotNull($service->parseCreditNumber($organisation));
    }

    public function testFindOrganisationNameByNameAndProject(): void
    {
        $container = new ServiceManager();
        $container->setService(EntityManager::class, $this->getEntityManagerMock());
        $container->setService(OrganisationSearchService::class, new OrganisationSearchService([]));
        $container->setService(TranslatorInterface::class, new Translator());
        $service = new OrganisationService($container);
        $organisation = new Organisation();
        $name = 'TestName';
        $project = new Project();
        $this->assertNull($service->findOrganisationNameByNameAndProject($organisation, $name, $project));
    }
}
