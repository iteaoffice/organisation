<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\View\Helper;

use Organisation\Acl\Assertion\Parent\Organisation as OrganisationAssertion;
use Organisation\Entity\Parent\Organisation;

/**
 * Class OrganisationLink
 *
 * @package Organisation\View\Helper
 */
class ParentOrganisationLink extends AbstractLink
{
    /**
     * @param Organisation|null $organisation
     * @param string $action
     * @param string $show
     *
     * @return string
     */
    public function __invoke(
        Organisation $organisation = null,
        $action = 'view',
        $show = 'text'
    ): string {
        $this->setParentOrganisation($organisation);
        $this->setAction($action);
        $this->setShow($show);

        if (!$this->hasAccess($this->getParentOrganisation(), OrganisationAssertion::class, $this->getAction())) {
            return '';
        }

        $this->addRouterParam('id', $this->getParentOrganisation()->getId());

        $this->setShowOptions(
            [
                'organisation'  => $this->getParentOrganisation()->getOrganisation(),
                'member-type'   => !$this->getParentOrganisation()->isEmpty() ? $this->getParentOrganisation()->getParent()->getType()->getType() : '',
                'member-status' => !$this->getParentOrganisation()->isEmpty() ? $this->getParentOrganisation()->getParent()->getStatus()->getStatus() : '',
            ]
        );

        return $this->createLink();
    }

    /**
     * Parse the action.
     */
    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'add-affiliation':
                $this->setRouter('zfcadmin/parent/organisation/add-affiliation');
                $this->setText($this->translate('txt-parent-organisation-add-affiliation'));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/parent/organisation/edit');
                $this->setText(sprintf($this->translate('txt-edit-organisation-%s'), $this->getOrganisation()));
                break;
            case 'list':
                $this->setRouter('zfcadmin/parent/organisation/list');
                $this->setText($this->translate('txt-list-organisations'));
                break;
            case 'view':
                $this->setRouter('zfcadmin/parent/organisation/view');
                $this->setText(sprintf($this->translate('txt-view-organisation-%s'), $this->getOrganisation()));
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        '%s is an incorrect action for %s',
                        $this->getAction(),
                        __CLASS__
                    )
                );
        }
    }
}
