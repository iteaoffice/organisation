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

use Organisation\Acl\Assertion\Type as TypeAssertion;
use Organisation\Entity\Type;

/**
 * Class TypeLink
 * @package Organisation\View\Helper
 */
class TypeLink extends AbstractLink
{
    /**
     * @param Type|null $type
     * @param string $action
     * @param string $show
     *
     * @return string
     */
    public function __invoke(
        Type $type = null,
        $action = 'view',
        $show = 'text'
    ): string {
        $this->setType($type);
        $this->setAction($action);
        $this->setShow($show);

        if (!$this->hasAccess($this->getType(), TypeAssertion::class, $this->getAction())) {
            return '';
        }

        $this->addRouterParam('id', $this->getType()->getId());

        $this->setShowOptions(
            [
                'type'        => $this->getType()->getType(),
                'description' => $this->getType()->getDescription(),
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
                $this->setRouter('zfcadmin/organisation-type/new');
                $this->setText($this->translate('txt-new-organisation-type'));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/organisation-type/edit');
                $this->setText(sprintf($this->translate('txt-edit-organisation-type-%s'), $this->getType()));
                break;
            case 'list':
                $this->setRouter('zfcadmin/organisation-type/list');
                $this->setText($this->translate('txt-list-organisation-types'));
                break;
            case 'view':
                $this->setRouter('zfcadmin/organisation-type/view');
                $this->setText(sprintf($this->translate('txt-view-organisation-type-%s'), $this->getType()));
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
