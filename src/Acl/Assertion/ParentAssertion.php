<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Acl\Assertion;

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Organisation\Entity\ParentEntity;

/**
 * Class ParentEntity
 *
 * @package Organisation\Acl\Assertion
 */
final class ParentAssertion extends AbstractAssertion
{
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ): bool {
        $this->setPrivilege($privilege);

        /** @var ParentEntity $parent */
        $parent = $resource;

        switch ($this->getPrivilege()) {
            case 'create-from-organisation':
                return ! $parent->getOrganisation()->hasParent();

            default:
                return $this->rolesHaveAccess('office');
        }
    }
}
