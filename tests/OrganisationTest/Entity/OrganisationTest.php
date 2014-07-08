<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProjectTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace OrganisationTest\Entity;

use Organisation\Entity\Organisation;

class OrganisationTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateEntity()
    {
        $organisation = new Organisation();
        $this->assertInstanceOf("Organisation\Entity\Organisation", $organisation);
    }
}
