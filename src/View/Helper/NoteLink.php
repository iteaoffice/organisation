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

use Organisation\Acl\Assertion\Note as NoteAssertion;
use Organisation\Entity\Note;
use Organisation\Entity\Organisation;

/**
 * Class NoteLink
 * @package Organisation\View\Helper
 */
class NoteLink extends AbstractLink
{
    /**
     * @var Note
     */
    private $note;

    /**
     * @param Note|null         $note
     * @param Organisation|null $organisation
     * @param string            $action
     * @param string            $show
     * @return string
     */
    public function __invoke(Note $note = null, Organisation $organisation = null, $action = 'edit', $show = 'icon') {
        $this->setNote($note);
        $this->setOrganisation($organisation);
        $this->setAction($action);
        $this->setShow($show);

        if (! $this->hasAccess($this->getNote(), NoteAssertion::class, $this->getAction())) {
            return '';
        }

        if (!$this->getNote()->isEmpty()) {
            $this->addRouterParam('id', $this->getNote()->getId());
        }

        return $this->createLink();
    }

    /**
     * Parse the action.
     */
    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/organisation/note/new');
                $this->addRouterParam('organisationId', $this->getOrganisation()->getId());
                $this->setText($this->translate('txt-new-note'));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/organisation/note/edit');
                $this->setText($this->translate('txt-edit-note'));
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('%s is an incorrect action for %s', $this->getAction(), __CLASS__)
                );
        }
    }

    /**
     * @return Note
     */
    public function getNote(): Note
    {
        if (is_null($this->note)) {
            $this->note = new Note();
        }
        return $this->note;
    }

    /**
     * @param Note|null $note
     * @return NoteLink
     */
    public function setNote(Note $note = null): NoteLink
    {
        $this->note = $note;
        return $this;
    }
}
