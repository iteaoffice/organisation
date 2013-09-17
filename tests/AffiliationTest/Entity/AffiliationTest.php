<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProjectTest
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 ITEA
 */
namespace OrganisationTest\Entity;

use Organisation\Entity\Organisation;

class ProjectTest extends \PHPUnit_Framework_TestCase
{

    public function testCanCreateEntity()
    {
        $organisation = new Organisation();
        $this->assertInstanceOf("Organisation\Entity\Organisation", $organisation);
    }
}
