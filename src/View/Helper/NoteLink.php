<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/general for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;
use Organisation\Entity\Note;
use Organisation\Entity\Organisation;

/**
 * Class NoteLink
 * @package General\View\Helper
 */
final class NoteLink extends AbstractLink
{
    public function __invoke(
        Note $note = null,
        string $action = 'view',
        string $show = 'name',
        Organisation $organisation = null
    ): string
    {
        $note ??= new Note();

        $routeParams = [];
        $showOptions = [];
        if (!$note->isEmpty()) {
            $routeParams['id'] = $note->getId();
            $showOptions['name'] = $note->getNote();
        }

        if (null !== $organisation) {
            $routeParams['organisationId'] = $organisation->getId();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fa-plus',
                    'route' => 'zfcadmin/organisation/note/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-note')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/organisation/note/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-note')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
