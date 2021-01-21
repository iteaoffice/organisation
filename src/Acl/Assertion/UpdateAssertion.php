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
use Organisation\Entity\Organisation;
use Organisation\Entity\Update;
use Organisation\Service\UpdateService;

/**
 * Class UpdateAssertion
 *
 * @package Organisation\Acl\Assertion
 */
final class UpdateAssertion extends AbstractAssertion
{
    private UpdateService $updateService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->updateService = $container->get(UpdateService::class);
    }

    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $update = null,
        $privilege = null
    ): bool {
        $this->setPrivilege($privilege);

        $organisation = null;
        if ($update instanceof Update) {
            $organisation = $update->getOrganisation();
        }

        if (! $update instanceof Update) {
            $organisationId = (int)$this->getRouteMatch()->getParam('organisationId', 0);
            /** @var Organisation $organisation */
            $organisation = $this->updateService->find(Organisation::class, $organisationId);

            // Allow editing from profile page
            if (
                ($organisation === null)
                && ($this->getRouteMatch()->getMatchedRouteName() === 'community/contact/profile/organisation')
            ) {
                $organisation = $this->contact->getContactOrganisation()->getOrganisation();
            }

            $update = new Update();
            if ($organisation instanceof Organisation) {
                $update->setOrganisation($organisation);
            }
        }

        switch ($this->getPrivilege()) {
            case 'view-admin':
            case 'edit-admin':
            case 'approve':
                return $this->rolesHaveAccess('office');
            default:
                // An organisation can only have 1 pending update at a time
                if (($organisation === null) || $this->updateService->hasPendingUpdates($organisation)) {
                    return false;
                }
                if ($organisation === $this->contact->getContactOrganisation()->getOrganisation()) {
                    return true;
                }
                if ($this->contactService->contactHasPermit($this->contact, 'edit', $update->getOrganisation())) {
                    return true;
                }

                return $this->rolesHaveAccess('office');
        }
    }
}
