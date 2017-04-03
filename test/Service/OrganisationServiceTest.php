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

use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;
use Project\Entity\Project;
use Project\Entity\Result\Result;
use Testing\Util\AbstractServiceTest;

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
    public function testCanCreateService()
    {
        $service = new OrganisationService();
        $this->assertInstanceOf(OrganisationService::class, $service);
    }

    public function testCanDeleteEmptyOrganisation()
    {
        $service = new OrganisationService();

        $organisation = new Organisation();
        $this->assertTrue($service->canDeleteOrganisation($organisation));
    }

    public function testCanNotDeleteOrganisation()
    {
        $service = new OrganisationService();

        $organisation = new Organisation();
        $organisation->getResult()->add(new Result());
        $this->assertFalse($service->canDeleteOrganisation($organisation));
    }

    public function testCanParseDebtorNumber()
    {
        $service = new OrganisationService();

        $organisation = new Organisation();
        $organisation->setId(1);
        $this->assertNotNull($service->parseDebtorNumber($organisation));
    }

    public function testCanParseCreditNumber()
    {
        $service = new OrganisationService();

        $organisation = new Organisation();
        $organisation->setId(1);
        $this->assertNotNull($service->parseCreditNumber($organisation));
    }

    public function testFindOrganisationNameByNameAndProject()
    {
        $service = new OrganisationService();

        $organisation = new Organisation();
        $name         = 'TestName';
        $project      = new Project();

        $this->assertNull($service->findOrganisationNameByNameAndProject($organisation, $name, $project));
    }
}