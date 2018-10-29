<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Organisation\Entity\Note;
use Organisation\Entity\Organisation;
use Zend\Navigation\Page\Mvc;

/**
 * Class OrganisationLabel
 *
 * @package Organisation\Navigation\Invokable
 */
class OrganisationLabel extends AbstractNavigationInvokable
{
    /**
     * @param Mvc $page
     *
     * @return void
     */
    public function __invoke(Mvc $page): void
    {
        if ($this->getEntities()->containsKey(Organisation::class)) {
            /** @var Organisation $organisation */
            $organisation = $this->getEntities()->get(Organisation::class);
            $page->setParams(array_merge(
                $page->getParams(),
                ['id' => $organisation->getId()]
            ));
            $label = (string)$organisation;
        // Get organisation from note
        } elseif ($this->getEntities()->containsKey(Note::class)) {
            /** @var Note $note */
            $note = $this->getEntities()->get(Note::class);
            $page->setParams(array_merge(
                $page->getParams(),
                ['id' => $note->getOrganisation()->getId()]
            ));
            $label = (string)$note->getOrganisation();
        } else {
            $label = $this->translator->translate('txt-nav-view');
        }
        $page->set('label', $label);
    }
}
