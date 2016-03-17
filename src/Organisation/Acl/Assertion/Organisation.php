<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

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
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param Acl $acl
     * @param RoleInterface $role
     * @param ResourceInterface $resource
     * @param string $privilege
     *
     * @return bool
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        $id = $this->getRouteMatch()->getParam('id');
        /*
         * When the privilege is_null (not given by the isAllowed helper), get it from the routeMatch
         */
        if (is_null($privilege)) {
            $privilege = $this->getRouteMatch()->getParam('privilege');
        }
        if (!$resource instanceof OrganisationEntity && !is_null($id)) {
            $resource = $this->getOrganisationService()->setOrganisationId($id)->getOrganisation();
        }

       

        switch ($privilege) {
            case 'view-community':
                if ($this->getContactService()->contactHasPermit($this->getContact(), 'view', $resource)) {
                    return true;
                }
                break;
            case 'edit-community':
                if ($this->getContactService()->contactHasPermit($this->getContact(), 'edit', $resource)) {
                    return true;
                }
                break;
        }

        return false;
    }
}
