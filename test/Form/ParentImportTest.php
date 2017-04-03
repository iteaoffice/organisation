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

use Organisation\Form\ParentImport;
use Testing\Util\AbstractFormTest;

/**
 * Class ParentImportTest
 *
 * @package OrganisationTest\Service
 */
class ParentImportTest extends AbstractFormTest
{
    public function testCanCreateForm()
    {
        $parentImport = new ParentImport();

        $this->assertInstanceOf(ParentImport::class, $parentImport);
        $this->assertTrue($parentImport->has('file'));
        $this->assertTrue($parentImport->has('upload'));
        $this->assertTrue($parentImport->has('import'));
        $this->assertArrayHasKey('file', $parentImport->getInputFilterSpecification());


    }

}