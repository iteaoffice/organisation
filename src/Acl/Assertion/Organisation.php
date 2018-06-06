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

use Organisation\Entity\Organisation as OrganisationEntity;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class Organisation.
 */
class Organisation extends AssertionAbstract
{
    /**
     * Returns true if and only if the assertion conditions are met.
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $organisation, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param Acl $acl
     * @param RoleInterface $role
     * @param ResourceInterface|OrganisationEntity $organisation
     * @param string $privilege
     *
     * @return bool
     */
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $organisation = null,
        $privilege = null
    ): bool {
        $this->setPrivilege($privilege);
        $id = $this->getId();

        if (!$organisation instanceof OrganisationEntity && !\is_null($id)) {
            $organisation = $this->getOrganisationService()->findOrganisationById((int) $id);
        }

        switch ($this->getPrivilege()) {
            case 'view-community':
                if ($this->getContactService()->contactHasPermit($this->getContact(), 'view', $organisation)) {
                    return true;
                }
                break;
            case 'edit-community':
                if ($this->getContactService()->contactHasPermit($this->getContact(), 'edit', $organisation)) {
                    return true;
                }
                break;
        }

        return false;
    }
}
