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

namespace OrganisationTest\Service;

use Contact\Entity\Contact;
use Organisation\Entity;
use Organisation\Repository;
use Organisation\Service\ParentService;
use Testing\Util\AbstractServiceTest;

/**
 * Class OrganisationServiceTest
 *
 * @package OrganisationTest\Service
 */
class ParentServiceTest extends AbstractServiceTest
{
    /**
     *
     */
    public function testCanCreateService()
    {
        $service = new ParentService();
        $this->assertInstanceOf(ParentService::class, $service);
    }

    /**
     *
     */
    public function testFindOrganisationInParentWhenOrganisationIsNotInParent()
    {
        $service = new ParentService();

        $organisation = new Entity\Organisation();
        $organisation->setId(1);

        $parent = new Entity\OParent();

        $this->assertNull($service->findParentOrganisationInParentByOrganisation($parent, $organisation));
    }

    public function testFindOrganisationInParentWhenOrganisationIsInParent()
    {
        $service = new ParentService();

        $organisation = new Entity\Organisation();
        $organisation->setId(1);

        $parentOrganisation = new \Organisation\Entity\Parent\Organisation();
        $parentOrganisation->setOrganisation($organisation);

        $parent = new Entity\OParent();
        $parent->getParentOrganisation()->add($parentOrganisation);

        $this->assertEquals($parentOrganisation,
            $service->findParentOrganisationInParentByOrganisation($parent, $organisation));
    }

    public function testCreateParentAndParentOrganisationFromOrganisationWithExistingParent()
    {
        $service = new ParentService();
        $service->setEntityManager($this->getEntityManagerMock());

        $organisation = new Entity\Organisation();
        $organisation->setId(1);

        $contact = new Contact();
        $contact->setId(1);

        $parent = new Entity\OParent();
        $organisation->setParent($parent);

        // Create a dummy project entity
        $typeId = 1;
        $type   = new Entity\Parent\Type();
        $type->setId($typeId);
        // Create a dummy project entity
        $statusId = 1;
        $status   = new Entity\Parent\Status();
        $status->setId($statusId);


        // Mock the repository, disabling the constructor
        $parentTypeRepositoryMock = $this->getMockBuilder(Repository\Parent\Type::class)
                                         ->disableOriginalConstructor()
                                         ->setMethods(['find', 'findOneBy'])
                                         ->getMock();
        $parentTypeRepositoryMock->expects($this->once())
                                 ->method('find')
                                 ->with($this->identicalTo($typeId), $this->identicalTo($statusId))
                                 ->will($this->returnValue($type), $this->returnValue($statusId));

        $entityManagerMock = $this->getEntityManagerMock(Repository\Parent\Type::class, $parentTypeRepositoryMock);
        $service->setEntityManager($entityManagerMock);

        $parentOrganisation = $service->createParentAndParentOrganisationFromOrganisation($organisation, $contact);
        $this->assertInstanceOf(\Organisation\Entity\Parent\Organisation::class, $parentOrganisation);

    }

    public function testCreateParentAndParentOrganisationFromOrganisationWithNonExistingParent()
    {
        $service = new ParentService();
        $service->setEntityManager($this->getEntityManagerMock());

        $organisation = new Entity\Organisation();
        $organisation->setId(1);

        $contact = new Contact();
        $contact->setId(1);

        $parentOrganisation = $service->createParentAndParentOrganisationFromOrganisation($organisation, $contact);
        $this->assertInstanceOf(Entity\Parent\Organisation::class, $parentOrganisation);
    }
}