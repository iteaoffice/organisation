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

use Interop\Container\ContainerInterface;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Organisation\Entity\Organisation as OrganisationEntity;
use Organisation\Service\OrganisationService;

/**
 * Class OrganisationAssertion
 * @package Organisation\Acl\Assertion
 */
final class OrganisationAssertion extends AbstractAssertion
{
    private OrganisationService $organisationService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->organisationService = $container->get(OrganisationService::class);
    }

    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $organisation = null,
        $privilege = null
    ): bool {
        $this->setPrivilege($privilege);
        $id = $this->getId();

        if (! $organisation instanceof OrganisationEntity && null !== $id) {
            $organisation = $this->organisationService->findOrganisationById((int)$id);
        }

        switch ($this->getPrivilege()) {
            case 'view':
                return true;
            case 'view-community':
                if ($this->contactService->contactHasPermit($this->contact, 'view', $organisation)) {
                    return true;
                }
                break;
            case 'edit-community':
                if ($this->contactService->contactHasPermit($this->contact, 'edit', $organisation)) {
                    return true;
                }
                break;
        }

        return $this->rolesHaveAccess('office');
    }
}
