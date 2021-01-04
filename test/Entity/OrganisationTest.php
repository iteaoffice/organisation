<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
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
