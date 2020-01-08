<?php

/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Acl\Assertion\Parent;

use Admin\Entity\Access;
use Organisation\Acl\Assertion\AbstractAssertion;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

/**
 * Class Organisation
 *
 * @package Organisation\Acl\Assertion\Parent
 */
final class Organisation extends AbstractAssertion
{
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ): bool {
        return $this->rolesHaveAccess('office');
    }
}
