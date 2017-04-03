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
}