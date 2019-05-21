<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace OrganisationTest\Service;

use Doctrine\ORM\EntityManager;
use Organisation\Entity;
use Organisation\Entity\Parent\Organisation;
use Organisation\Service\ParentService;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Testing\Util\AbstractServiceTest;
use Zend\ServiceManager\ServiceManager;

/**
 * Class OrganisationServiceTest
 *
 * @package OrganisationTest\Service
 */
class ParentServiceTest extends AbstractServiceTest
{
    public function testFindOrganisationInParentWhenOrganisationIsNotInParent(): void
    {
        $container = new ServiceManager();
        $container->setService(EntityManager::class, $this->getEntityManagerMock());
        $projectService = $this->getMockBuilder(ProjectService::class)->disableOriginalConstructor()->getMock();
        $versionService = $this->getMockBuilder(VersionService::class)->disableOriginalConstructor()->getMock();
        $container->setService(ProjectService::class, $projectService);
        $container->setService(VersionService::class, $versionService);


        $service = new ParentService($container);

        $organisation = new Entity\Organisation();
        $organisation->setId(1);

        $parent = new Entity\OParent();

        $this->assertNull($service->findParentOrganisationInParentByOrganisation($parent, $organisation));
    }

    public function testFindOrganisationInParentWhenOrganisationIsInParent(): void
    {
        $container = new ServiceManager();
        $container->setService(EntityManager::class, $this->getEntityManagerMock());
        $projectService = $this->getMockBuilder(ProjectService::class)->disableOriginalConstructor()->getMock();
        $versionService = $this->getMockBuilder(VersionService::class)->disableOriginalConstructor()->getMock();
        $container->setService(ProjectService::class, $projectService);
        $container->setService(VersionService::class, $versionService);
        $service = new ParentService($container);

        $organisation = new Entity\Organisation();
        $organisation->setId(1);

        $parentOrganisation = new Organisation();
        $parentOrganisation->setOrganisation($organisation);

        $parent = new Entity\OParent();
        $parent->getParentOrganisation()->add($parentOrganisation);

        $this->assertEquals(
            $parentOrganisation,
            $service->findParentOrganisationInParentByOrganisation($parent, $organisation)
        );
    }
}