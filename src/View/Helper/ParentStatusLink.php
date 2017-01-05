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
 */

namespace Organisation\View\Helper;

use Organisation\Acl\Assertion\Parent\Status as ParentStatusAssertion;
use Organisation\Entity;

/**
 * Class ParentStatusLink
 *
 * @package Organisation\View\Helper
 */
class ParentStatusLink extends AbstractLink
{
    /**
     * @param Entity\Parent\Status|null $parentStatus
     * @param string                    $action
     * @param string                    $show
     *
     * @return string
     */
    public function __invoke(
        Entity\Parent\Status $parentStatus = null,
        $action = 'view',
        $show = 'text'
    ): string {
        $this->setParentStatus($parentStatus);
        $this->setAction($action);
        $this->setShow($show);

        if (! $this->hasAccess($this->getParentStatus(), ParentStatusAssertion::class, $this->getAction())) {
            return '';
        }

        $this->addRouterParam('id', $this->getParentStatus()->getId());

        $this->setShowOptions(
            [
                'status' => $this->getParentStatus(),
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
            case 'new':
                $this->setRouter('zfcadmin/parent-status/new');
                $this->setText($this->translate('txt-new-parent-status'));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/parent-status/edit');
                $this->setText(sprintf($this->translate('txt-edit-parent-status-%s'), $this->getParentStatus()));
                break;
            case 'list':
                $this->setRouter('zfcadmin/parent-status/list');
                $this->setText($this->translate('txt-list-parent-status'));
                break;
            case 'view':
                $this->setRouter('zfcadmin/parent-status/view');
                $this->setText(sprintf($this->translate('txt-view-parent-status-%s'), $this->getParentStatus()));
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
