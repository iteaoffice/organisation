<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProjectTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace OrganisationTest\Entity;

use Organisation\Entity\Organisation;
use PHPUnit\Framework\TestCase;

/**
 * Class OrganisationTest
 *
 * @package OrganisationTest\Entity
 */
class OrganisationTest extends TestCase
{
    /**
     *
     */
    public function testCanCreateEntity()
    {
        $organisation = new Organisation();
        $this->assertInstanceOf(Organisation::class, $organisation);
    }
}
