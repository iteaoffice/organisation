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
use Organisation\Entity\Organisation as OrganisationEntity;
use Organisation\Entity\Update;
use Organisation\Service\OrganisationService;
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
     * @var OrganisationService
     */
    private $organisationService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->organisationService = $container->get(OrganisationService::class);
    }

    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $update = null,
        $privilege = null
    ): bool {
        $this->setPrivilege($privilege);

        if (!($update instanceof Update)) {
            $update = new Update();
            $organisationId = (int) $this->getRouteMatch()->getParam('organisationId', 0);
            $organisation = $this->organisationService->find(OrganisationEntity::class, $organisationId);
            if ($organisation instanceof OrganisationEntity) {
                $update->setOrganisation($organisation);
            }
        }

        switch ($this->getPrivilege()) {
            case 'new':
                if ($this->contactService->contactHasPermit($this->contact, 'edit', $update->getOrganisation())) {
                    return true;
                }
                break;
        }

        return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
    }
}
