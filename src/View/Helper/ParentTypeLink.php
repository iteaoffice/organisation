<?php

/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Organisation\Acl\Assertion\Parent\Type as ParentTypeAssertion;
use Organisation\Entity;

/**
 * Class ParentTypeLink
 *
 * @package Organisation\View\Helper
 */
class ParentTypeLink extends AbstractLink
{
    /**
     * @param Entity\Parent\Type|null $parentType
     * @param string $action
     * @param string $show
     *
     * @return string
     */
    public function __invoke(
        Entity\Parent\Type $parentType = null,
        $action = 'view',
        $show = 'text'
    ): string {
        $this->setParentType($parentType);
        $this->setAction($action);
        $this->setShow($show);

        if (!$this->hasAccess($this->getParentType(), ParentTypeAssertion::class, $this->getAction())) {
            return '';
        }

        $this->addRouterParam('id', $this->getParentType()->getId());

        $this->setShowOptions(
            [
                'type' => (string) $this->getParentType(),
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
            case 'new':
                $this->setRouter('zfcadmin/parent-type/new');
                $this->setText($this->translator->translate('txt-new-parent-type'));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/parent-type/edit');
                $this->setText(sprintf($this->translator->translate('txt-edit-parent-type-%s'), $this->getParentType()));
                break;
            case 'list':
                $this->setRouter('zfcadmin/parent-type/list');
                $this->setText($this->translator->translate('txt-list-parent-types'));
                break;
            case 'view':
                $this->setRouter('zfcadmin/parent-type/view');
                $this->setText(sprintf($this->translator->translate('txt-view-parent-type-%s'), $this->getParentType()));
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
