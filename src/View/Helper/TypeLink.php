<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\View\Helper;

use Organisation\Acl\Assertion\Type as TypeAssertion;
use Organisation\Entity\Type;

/**
 * Create a link to an document.
 *
 * @category  Type
 *
 * @author    Johan van der Heide < johan . van . der . heide@itea3 . org >
 * @copyright 2004 - 2014 ITEA Office
 * @license   https://itea3.org/license.txt proprietary
 *
 * @link      https://itea3.org
 */
class TypeLink extends LinkAbstract
{
    /**
     * @param Type|null $type
     * @param string           $action
     * @param string           $show
     *
     * @return string
     */
    public function __invoke(
        Type $type = null,
        $action = 'view',
        $show = 'text'
    ) {
        $this->setType($type);
        $this->setAction($action);
        $this->setShow($show);

        if (!$this->hasAccess($this->getType(), TypeAssertion::class, $this->getAction())) {
            return '';
        }

        $this->addRouterParam('id', $this->getType()->getId());

        $this->setShowOptions([
            'type' => $this->getType(),
        ]);

        return $this->createLink();
    }

    /**
     * Parse the action.
     */
    public function parseAction()
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
                throw new \InvalidArgumentException(sprintf(
                    '%s is an incorrect action for %s',
                    $this->getAction(),
                    __CLASS__
                ));
        }
    }
}
