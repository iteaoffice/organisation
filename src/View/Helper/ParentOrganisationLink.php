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
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

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
                'organisation'  => (string)$this->getParentOrganisation()->getOrganisation(),
                'member-type'   => !$this->getParentOrganisation()->isEmpty() ? $this->getParentOrganisation()
                    ->getParent()->getType()->getType() : '',
                'member-status' => !$this->getParentOrganisation()->isEmpty() ? $this->translate(
                    $this->getParentOrganisation()->getParent()->getMemberType(true)
                ) : '',
            ]
        );

        return $this->createLink();
    }

    /**
     * Parse the action.
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'add-affiliation':
                $this->setRouter('zfcadmin/parent/organisation/add-affiliation');
                $this->setText($this->translate('txt-parent-organisation-add-affiliation'));
                break;
            case 'merge':
                $this->setRouter('zfcadmin/parent/organisation/merge');
                $this->setText($this->translate('txt-merge-parent-organisation'));
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
