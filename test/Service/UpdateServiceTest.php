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
    public function testFindPendingUpdates()
    {
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findBy'])
            ->getMock();
        $repositoryMock->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(['dateApproved' => null]), $this->equalTo(['dateCreated' => Criteria::ASC]))
            ->willReturn([]);
/** @var EntityManager $entityManagerMock */
        $entityManagerMock = $this->getEntityManagerMock(Update::class, $repositoryMock);
/** @var EmailService $emailServiceMock */
        $emailServiceMock = $this->getEmailServiceMock();
        $service = new UpdateService($entityManagerMock, $emailServiceMock);
        $this->assertEquals([], $service->findPendingUpdates());
    }

    public function testCountPendingUpdates()
    {
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['count'])
            ->getMock();
        $repositoryMock->expects($this->once())
            ->method('count')
            ->with($this->equalTo(['dateApproved' => null]))
            ->willReturn(1);
/** @var EntityManager $entityManagerMock */
        $entityManagerMock = $this->getEntityManagerMock(Update::class, $repositoryMock);
/** @var EmailService $emailServiceMock */
        $emailServiceMock = $this->getEmailServiceMock();
        $service = new UpdateService($entityManagerMock, $emailServiceMock);
        $this->assertEquals(1, $service->countPendingUpdates());
    }

    public function testHasUpdates()
    {
        $organisation = new Organisation();
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['count'])
            ->getMock();
        $repositoryMock->expects($this->once())
            ->method('count')
            ->with($this->equalTo(['dateApproved' => null, 'organisation' => $organisation]))
            ->willReturn(1);
/** @var EntityManager $entityManagerMock */
        $entityManagerMock = $this->getEntityManagerMock(Update::class, $repositoryMock);
/** @var EmailService $emailServiceMock */
        $emailServiceMock = $this->getEmailServiceMock();
        $service = new UpdateService($entityManagerMock, $emailServiceMock);
        $this->assertTrue($service->hasPendingUpdates($organisation));
    }

    public function testApproveUpdate()
    {
        $organisation     = new Organisation();
        $organisationType = new Type();
        $organisationType->setId(1);
        $contact          = new Contact();
        $contentType      = new ContentType();
        $contentType->setId(1);
        $logo             = new UpdateLogo();
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
/** @var EntityManager $entityManagerMock */
        $entityManagerMock = $this->getEntityManagerMock();
/** @var EmailService|MockObject $emailServiceMock */
        $emailServiceMock = $this->getEmailServiceMock();
        $emailServiceMock->expects($this->once())
            ->method('addTo')
            ->with($contact);
        $service = new UpdateService($entityManagerMock, $emailServiceMock);
        $result = $service->approveUpdate($update);
/** @var Logo $logo */
        $logo = $organisation->getLogo()->first();
        $this->assertTrue($result);
        $this->assertInstanceOf(DateTime::class, $update->getDateApproved());
        $this->assertEquals(1, $organisation->getType()->getId());
        $this->assertEquals('Test', $organisation->getDescription()->getDescription());
        $this->assertEquals(1, $logo->getContentType()->getId());
        $this->assertEquals('jpg', $logo->getLogoExtension());
        $this->assertEquals('some-binary-string', $logo->getOrganisationLogo());
    }
}
