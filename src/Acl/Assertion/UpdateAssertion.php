<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Acl\Assertion;

use Admin\Entity\Access;
use Interop\Container\ContainerInterface;
use Organisation\Entity\Organisation;
use Organisation\Entity\Update;
use Organisation\Service\UpdateService;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class UpdateAssertion
 * @package Organisation\Acl\Assertion
 */
final class UpdateAssertion extends AbstractAssertion
{
    /**
     * @var UpdateService
     */
    private $updateService;

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

        $organisation = $update->getOrganisation();

        if ($organisation === null) {
            $organisationId = (int) $this->getRouteMatch()->getParam('organisationId', 0);
            /** @var Organisation $organisation */
            $organisation = $this->updateService->find(Organisation::class, $organisationId);

            // Allow editing from profile page
            if (($organisation === null)
                && ($this->getRouteMatch()->getMatchedRouteName() === 'community/contact/profile/organisation')) {
                $organisation = $this->contact->getContactOrganisation()->getOrganisation();
            }

            $update = new Update();
            if ($organisation instanceof Organisation) {
                $update->setOrganisation($organisation);
            }
        }

        switch ($this->getPrivilege()) {
            case 'edit':
                // An organisation can only have 1 pending update at a time
                if (($organisation === null) || $this->updateService->hasPendingUpdates($organisation)) {
                    return false;
                }
                if ($this->contactService->contactHasPermit($this->contact, 'edit', $update->getOrganisation())) {
                    return true;
                }
                break;
        }

        return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
    }
}
