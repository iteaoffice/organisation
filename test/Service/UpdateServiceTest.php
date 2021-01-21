<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace OrganisationTest\Service;

use Contact\Entity\Contact;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use General\Entity\ContentType;
use General\Service\EmailService;
use Organisation\Entity\Logo;
use Organisation\Entity\Organisation;
use Organisation\Entity\Type;
use Organisation\Entity\Update;
use Organisation\Entity\UpdateLogo;
use Organisation\Service\UpdateService;
use PHPUnit\Framework\MockObject\MockObject;
use Testing\Util\AbstractServiceTest;

/**
 * Class UpdateServiceTest
 * @package OrganisationTest\Service
 */
class UpdateServiceTest extends AbstractServiceTest
{
    public function testFindPendingUpdates(): void
    {
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findBy'])
            ->getMock();
        $repositoryMock->expects(self::once())
            ->method('findBy')
            ->with(self::equalTo(['dateApproved' => null]), self::equalTo(['dateCreated' => Criteria::ASC]))
            ->willReturn([]);
        $entityManagerMock = $this->getEntityManagerMock(Update::class, $repositoryMock);
        $emailServiceMock  = $this->getEmailServiceMock();
        $service           = new UpdateService($entityManagerMock, $emailServiceMock);
        self::assertEquals([], $service->findPendingUpdates());
    }

    public function testCountPendingUpdates(): void
    {
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['count'])
            ->getMock();
        $repositoryMock->expects(self::once())
            ->method('count')
            ->with(self::equalTo(['dateApproved' => null]))
            ->willReturn(1);

        $entityManagerMock = $this->getEntityManagerMock(Update::class, $repositoryMock);
        $emailServiceMock  = $this->getEmailServiceMock();
        $service           = new UpdateService($entityManagerMock, $emailServiceMock);

        self::assertEquals(1, $service->countPendingUpdates());
    }

    public function testHasUpdates(): void
    {
        $organisation   = new Organisation();
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['count'])
            ->getMock();
        $repositoryMock->expects(self::once())
            ->method('count')
            ->with(self::equalTo(['dateApproved' => null, 'organisation' => $organisation]))
            ->willReturn(1);

        $entityManagerMock = $this->getEntityManagerMock(Update::class, $repositoryMock);
        $emailServiceMock = $this->getEmailServiceMock();

        $service          = new UpdateService($entityManagerMock, $emailServiceMock);
        self::assertTrue($service->hasPendingUpdates($organisation));
    }

    public function testApproveUpdate(): void
    {
        $organisation     = new Organisation();
        $organisationType = new Type();
        $organisationType->setId(1);
        $contact     = new Contact();
        $contentType = new ContentType();
        $contentType->setId(1);
        $logo = new UpdateLogo();
        $logo->setId(1);
        $logo->setContentType($contentType);
        $logo->setLogoExtension('jpg');
        $logo->setOrganisationLogo('some-binary-string');
        $update = new Update();
        $update->setContact($contact);
        $update->setOrganisation($organisation);
        $update->setDescription('Test');
        $update->setType($organisationType);
        $update->setLogo($logo);

        $entityManagerMock = $this->getEntityManagerMock();
        /** @var EmailService|MockObject $emailServiceMock */
        $emailServiceMock = $this->getEmailServiceMock();

        $service = new UpdateService($entityManagerMock, $emailServiceMock);
        $result  = $service->approveUpdate($update);
        /** @var Logo $logo */
        $logo = $organisation->getLogo()->first();
        self::assertTrue($result);
        self::assertInstanceOf(DateTime::class, $update->getDateApproved());
        self::assertEquals(1, $organisation->getType()->getId());
        self::assertEquals('Test', $organisation->getDescription()->getDescription());
        self::assertEquals(1, $logo->getContentType()->getId());
        self::assertEquals('jpg', $logo->getLogoExtension());
        self::assertEquals('some-binary-string', $logo->getOrganisationLogo());
    }
}
