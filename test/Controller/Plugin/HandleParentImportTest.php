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

use Contact\Entity\AddressType;
use Contact\Entity\Contact;
use Contact\Service\ContactService;
use General\Entity\Country;
use General\Service\GeneralService;
use Organisation\Controller\Plugin\HandleParentImport;
use Organisation\Entity\Organisation;
use Organisation\Entity\Parent\Status;
use Organisation\Entity\Parent\Type;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;
use Testing\Util\AbstractServiceTest;

/**
 * Class HandleParentImportTest
 *
 * @package OrganisationTest\Controller\Plugin
 */
class HandleParentImportTest extends AbstractServiceTest
{
    /**
     * @var HandleParentImport
     */
    protected $handleParentImport;

    /**
     * Set up basic properties
     */
    public function setUp()
    {
        $this->handleParentImport = new HandleParentImport();

        $this->handleParentImport->setEntityManager($this->getEntityManagerMock());
    }

    public function testCanCreatePlugin()
    {
        $this->assertInstanceOf(HandleParentImport::class, $this->handleParentImport);
    }

    public function testHasNoErrorsFunction()
    {
        $this->assertNotNull($this->handleParentImport->hasErrors());
    }

    public function testCanSetData()
    {
        $data = file_get_contents(__DIR__ . '/../../input/parents_and_financial_data.txt');

        $this->handleParentImport->setData($data);

        $this->assertNotEmpty($this->handleParentImport->getContent());
        $this->assertEmpty($this->handleParentImport->getErrors());
    }

    public function testCanValidateData()
    {
        $data = file_get_contents(__DIR__ . '/../../input/parents_and_financial_data.txt');
        $this->handleParentImport->setData($data);

        $country = new Country();

        //Mock the GeneralService for the country lookup
        /** @var GeneralService| $generalServiceMock */
        $generalServiceMock = $this->getMockBuilder(GeneralService::class)
            ->setMethods(['findCountryByCD'])
            ->getMock();

        $generalServiceMock->expects($this->any())
            ->method('findCountryByCD')
            ->with($this->isType('string'))
            ->will($this->returnValue($country));

        $this->handleParentImport->setGeneralService($generalServiceMock);


        /** @var ParentService $parentService */
        $parentService = $this->getMockBuilder(ParentService::class)
            ->setMethods(['findParentTypeByName', 'findParentStatusByName'])
            ->getMock();

        $parentType = new Type();

        $parentService->expects($this->any())
            ->method('findParentTypeByName')
            ->with($this->isType('string'))
            ->will($this->returnValue($parentType));
        $status = new Status();
        $parentService->expects($this->any())
            ->method('findParentStatusByName')
            ->with($this->isType('string'))
            ->will($this->returnValue($status));

        $this->handleParentImport->setParentService($parentService);


        $this->handleParentImport->validateData();

        $this->assertEmpty($this->handleParentImport->getErrors());
        $this->assertNotEmpty($this->handleParentImport->getContent());
    }

    public function testCanPrepareData()
    {
        $data = file_get_contents(__DIR__ . '/../../input/parents_and_financial_data.txt');

        $this->handleParentImport->setData($data);

        //Mock the GeneralService for the country lookup
        /** @var GeneralService| $generalServiceMock */
        $generalServiceMock = $this->getMockBuilder(GeneralService::class)
            ->setMethods(['findCountryByCD'])
            ->getMock();

        $country = new Country();
        $generalServiceMock->expects($this->any())
            ->method('findCountryByCD')
            ->with($this->isType('string'))
            ->will($this->returnValue($country));

        $this->handleParentImport->setGeneralService($generalServiceMock);

        /** @var ParentService $parentService */
        $parentService = $this->getMockBuilder(ParentService::class)
            ->setMethods(['findParentTypeByName', 'findParentStatusByName', 'findEntityById'])
            ->getMock();

        $parentType = new Type();

        $parentService->expects($this->any())
            ->method('findParentTypeByName')
            ->with($this->isType('string'))
            ->will($this->returnValue($parentType));
        $status = new Status();
        $parentService->expects($this->any())
            ->method('findParentStatusByName')
            ->with($this->isType('string'))
            ->will($this->returnValue($status));
        $parentService->expects($this->any())
            ->method('findEntityById')
            ->with($this->isType('string'), $this->isType('int'))
            ->will($this->returnValue($parentType), $this->returnValue($status));

        $this->handleParentImport->setParentService($parentService);


        /** @var OrganisationService $organisationService */
        $organisationService = $this->getMockBuilder(OrganisationService::class)
            ->setMethods(['findOrganisationByNameCountry', 'findEntityById'])
            ->getMock();

        $organisation = new Organisation();

        $organisationService->expects($this->any())
            ->method('findOrganisationByNameCountry')
            ->with($this->isType('string'), $this->isInstanceOf(Country::class))
            ->will($this->returnValue($organisation));
        $this->handleParentImport->setOrganisationService($organisationService);

        /** @var ContactService $contactService */
        $contactService = $this->getMockBuilder(ContactService::class)
            ->setMethods(['findEntityById', 'findContactByEmail'])
            ->getMock();

        $contact = new Contact();

        $contactService->expects($this->any())
            ->method('findContactByEmail')
            ->with($this->isType('string'))
            ->will($this->returnValue($contact));

        $contactService->expects($this->any())
            ->method('findEntityById')
            ->with(AddressType::class, $this->isType('int'))
            ->will($this->returnValue(new AddressType()));

        $this->handleParentImport->setContactService($contactService);


        $this->handleParentImport->validateData();

        $this->handleParentImport->prepareContent();

        $this->assertEmpty($this->handleParentImport->getErrors());
        $this->assertNotEmpty($this->handleParentImport->getParents());
    }
}