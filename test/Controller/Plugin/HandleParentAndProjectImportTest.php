<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace OrganisationTest\Controller\Plugin;

use Contact\Entity\Contact;
use Contact\Service\ContactService;
use General\Entity\Country;
use General\Entity\Gender;
use General\Service\GeneralService;
use Organisation\Controller\Plugin\HandleParentAndProjectImport;
use Organisation\Entity\Organisation;
use Organisation\Entity\Parent\Status;
use Organisation\Entity\Parent\Type;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Project\Entity\Project;
use Project\Service\ProjectService;
use Testing\Util\AbstractServiceTest;

/**
 * Class HandleParentImportTest
 *
 * @package OrganisationTest\Controller\Plugin
 */
class HandleParentAndProjectImportTest extends AbstractServiceTest
{


    /**
     * @var HandleParentAndProjectImport
     */
    protected $handleParentAndProjectImport;


    /**
     * Set up basic properties
     */
    public function setUp()
    {
        $this->handleParentAndProjectImport = new HandleParentAndProjectImport();
        $this->handleParentAndProjectImport->setEntityManager($this->getEntityManagerMock());

        //Mock the GeneralService for the country lookup
        $generalServiceMock = $this->getMockBuilder(GeneralService::class)
                                   ->setMethods([
                                       'findEntityById',
                                       'findCountryByCD',
                                   ])
                                   ->getMock();

        $gender = new Gender();
        $gender->setId(Gender::GENDER_UNKNOWN);

        $generalServiceMock->expects($this->any())
                           ->method('findEntityById')
                           ->with(
                               [
                                   $this->identicalTo(Gender::class),
                                   $this->identicalTo(Gender::GENDER_UNKNOWN),
                               ])
                           ->will($this->returnValue($gender));

        $generalServiceMock->expects($this->any())
                           ->method('findCountryByCD')
                           ->will($this->returnValue(new Country()));

        $this->handleParentAndProjectImport->setGeneralService($generalServiceMock);
    }

    /**
     *
     */
    public function testCanCreatePlugin()
    {
        $this->assertInstanceOf(HandleParentAndProjectImport::class, $this->handleParentAndProjectImport);
    }

    public function testCanSetData()
    {
        $data = file_get_contents(__DIR__ . '/../../input/ecsel_call_and_costs.txt');
        $this->handleParentAndProjectImport->setData($data);

        $this->assertNotEmpty($this->handleParentAndProjectImport->getContent());
        $this->assertEmpty($this->handleParentAndProjectImport->getWarnings());
    }

    public function testCanValidateCorrectData()
    {
        $data = file_get_contents(__DIR__ . '/../../input/ecsel_call_and_costs_correct.txt');
        $this->handleParentAndProjectImport->setData($data);


        $parentStatus  = new Status();
        $parentService = $this->getMockBuilder(ParentService::class)
                              ->setMethods([
                                  'findEntityById',
                              ])->getMock();

        $parentService->expects($this->any())
                      ->method('findEntityById')
                      ->with($this->equalTo(Status::class), $this->isType('int'))
                      ->will($this->returnValue($parentStatus));

        $this->handleParentAndProjectImport->setParentService($parentService);

        $this->assertTrue($this->handleParentAndProjectImport->validateData());
        $this->assertEmpty($this->handleParentAndProjectImport->getErrors());
    }

    /**
     * Test the validation with an incorrect email address
     */
    public function testCanValidateIncorrectEmailAddress()
    {
        $data = file_get_contents(__DIR__ . '/../../input/ecsel_call_and_costs_incorrect_email.txt');
        $this->handleParentAndProjectImport->setData($data);

        //Mock the GeneralService for the country lookup
        $generalServiceMock = $this->getMockBuilder(GeneralService::class)
                                   ->setMethods(['findCountryByCD'])
                                   ->getMock();

        $country = new Country();
        $generalServiceMock->expects($this->any())
                           ->method('findCountryByCD')
                           ->with($this->isType('string'))
                           ->will($this->returnValue($country));

        $this->handleParentAndProjectImport->setGeneralService($generalServiceMock);

        $parentStatus  = new Status();
        $parentService = $this->getMockBuilder(ParentService::class)
                              ->setMethods([
                                  'findEntityById',
                              ])->getMock();

        $parentService->expects($this->any())
                      ->method('findEntityById')
                      ->with($this->equalTo(Status::class), $this->isType('int'))
                      ->will($this->returnValue($parentStatus));

        $this->handleParentAndProjectImport->setParentService($parentService);

        $this->assertTrue($this->handleParentAndProjectImport->validateData());
        $this->assertNotEmpty($this->handleParentAndProjectImport->getErrors());
    }

    /**
     *
     */
    public function testCanPrepareContent()
    {
        $data = file_get_contents(__DIR__ . '/../../input/ecsel_call_and_costs.txt');
        $this->handleParentAndProjectImport->setData($data);


        //Mock the GeneralService for the country lookup
        $contactServiceMock = $this->getMockBuilder(ContactService::class)
                                   ->setMethods([
                                       'findContactByEmail',
                                       'findContactById',
                                   ])
                                   ->getMock();

        $contact = new Contact();

        $contactServiceMock->expects($this->any())
                           ->method('findContactByEmail')
                           ->with($this->anything())
                           ->willReturn($contact);
//        $contactServiceMock->expects($this->any())
//                           ->method('findContactByEmail')
//                           ->with($this->isEmpty())
//                           ->willReturn(null);

        $contactServiceMock->expects($this->any())
                           ->method('findContactById')
                           ->with($this->equalTo(1))
                           ->willReturn($contact);

        $this->handleParentAndProjectImport->setContactService($contactServiceMock);


        //Mock the program service
        $program            = new Program();
        $programServiceMock = $this->getMockBuilder(ProgramService::class)
                                   ->setMethods(['findProgramByName',])->getMock();
        $programServiceMock->expects($this->any())
                           ->method('findProgramByName')
                           ->with($this->anything())
                           ->will($this->returnValue($program));

        $this->handleParentAndProjectImport->setProgramService($programServiceMock);

        //Mock the call service
        $call            = new Call();
        $callServiceMock = $this->getMockBuilder(CallService::class)
                                ->setMethods(['findCallByName',])->getMock();
        $callServiceMock->expects($this->any())
                        ->method('findCallByName')
                        ->with($this->anything())
                        ->will($this->returnValue($call));

        $this->handleParentAndProjectImport->setCallService($callServiceMock);

        //Mock the project service
        $project            = new Project();
        $projectServiceMock = $this->getMockBuilder(ProjectService::class)
                                   ->setMethods(['findProjectByName',])->getMock();
        $projectServiceMock->expects($this->any())
                           ->method('findProjectByName')
                           ->with($this->anything())
                           ->will($this->returnValue($project));

        $this->handleParentAndProjectImport->setProjectService($projectServiceMock);

        //Mock the organisation service
        $organisation            = new Organisation();
        $organisationServiceMock = $this->getMockBuilder(OrganisationService::class)
                                        ->setMethods(['findOrganisationByNameCountry',])->getMock();
        $organisationServiceMock->expects($this->any())
                                ->method('findOrganisationByNameCountry')
                                ->with($this->anything())
                                ->will($this->returnValue($organisation));

        $this->handleParentAndProjectImport->setOrganisationService($organisationServiceMock);

        $parentStatus = new Status();

        $parentService = $this->getMockBuilder(ParentService::class)
                              ->setMethods([
                                  'findParentTypeByName',
                                  'findEntityById',
                              ])->getMock();

        $parentStatus = new Status();
        $parentService->expects($this->any())
                      ->method('findEntityById')
                      ->with(
                          $this->equalTo(Status::class), $this->isType('int')
                      )
                      ->will($this->returnValue($parentStatus));

        $parentType = new Type();
        $parentService->expects($this->any())
                      ->method('findParentTypeByName')
                      ->with($this->isType('string'))->will($this->returnValue($parentType));

        $this->handleParentAndProjectImport->setParentService($parentService);

        $this->handleParentAndProjectImport->validateData();
        $this->handleParentAndProjectImport->prepareContent();

        $this->assertEmpty($this->handleParentAndProjectImport->getErrors());
        $this->assertEmpty($this->handleParentAndProjectImport->getWarnings());

    }

}