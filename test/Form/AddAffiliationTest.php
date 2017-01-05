<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Affiliation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace OrganisationTest\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Organisation\Entity\Parent\Organisation;
use Organisation\Form\AddAffiliation;
use Project\Entity\Project;
use Project\Service\ProjectService;
use Testing\Util\AbstractFormTest;

/**
 * Class AddAffiliationTest
 *
 * @package AffiliationTest\Service
 */
class AddAffiliationTest extends AbstractFormTest
{
    /**
     * Set up basic properties
     */
    public function setUp()
    {

    }

    /**
     *
     */
    public function testCanCreateAddAffiliationForm()
    {
        return $this->markTestIncomplete('Not fixed yet, need to configure the mock builde');
        $parentOrganisation = new Organisation();

        $project = new Project();

        /** @var EntityManager $entityManager */
        $entityManager = $this->getEntityManagerMock();

        $projectServiceMock = $this->getMockBuilder(ProjectService::class)
                                   ->setMethods([
                                       'findProjectByParentOrganisation',
                                       'findAllProjects',
                                   ])
                                   ->getMock();

        $projectServiceMock->expects($this->exactly(1))
                           ->method('findProjectByParentOrganisation')
                           ->with(
                               $this->identicalTo($parentOrganisation)
                           )
                           ->will($this->returnValue([$project]));

        $projectServiceMock->expects($this->exactly(1))
                           ->method('findAllProjects')
                           ->will($this->returnValue(new Query($entityManager)));

        $addAffiliation = new AddAffiliation($projectServiceMock, $parentOrganisation);

        $this->assertInstanceOf(AddAffiliation::class, $addAffiliation);
    }


}