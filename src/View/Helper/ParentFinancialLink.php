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

use Organisation\Acl\Assertion\Parent\Financial as ParentFinancialAssertion;
use Organisation\Entity;

/**
 * Class ParentLink
 *
 * @package Organisation\View\Helper
 */
class ParentFinancialLink extends AbstractLink
{
    /**
     * @param Entity\Parent\Financial|null $financial
     * @param string $action
     * @param string $show
     * @param Entity\OParent|null $parent
     * @return string
     */
    public function __invoke(
        Entity\Parent\Financial $financial = null,
        $action = 'view',
        $show = 'text',
        Entity\OParent $parent = null
    ): string {
        $this->setFinancial($financial);
        $this->setParent($parent);
        $this->setAction($action);
        $this->setShow($show);

        if (!$this->hasAccess($this->getFinancial(), ParentFinancialAssertion::class, $this->getAction())) {
            return '';
        }

        if (!\is_null($financial)) {
            $this->setShowOptions(
                [
                    'organisation' => $this->getFinancial()->getOrganisation(),
                ]
            );
        }

        $this->addRouterParam('id', $this->getFinancial()->getId());
        $this->addRouterParam('parentId', $this->getParent()->getId());

        return $this->createLink();
    }

    /**
     * Parse the action.
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/parent/financial/new');
                $this->setText(sprintf($this->translate('txt-new-financial-for-%s'), $this->getParent()));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/parent/financial/edit');
                $this->setText(sprintf($this->translate('txt-edit-parent-financial-%s'), $this->getFinancial()));
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
