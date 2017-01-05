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

use Organisation\Controller\Plugin\HandleParentImport;
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
        $data = file_get_contents(__DIR__ . '/../../input/parents.txt');

        $this->handleParentImport->setData($data);

        $this->assertNotEmpty($this->handleParentImport->getContent());
        $this->assertEmpty($this->handleParentImport->getWarnings());
    }

    public function testCanPrepareData()
    {
        $data = file_get_contents(__DIR__ . '/../../input/parents.txt');
        $this->handleParentImport->setData($data);

        $this->handleParentImport->prepareContent();
    }
}