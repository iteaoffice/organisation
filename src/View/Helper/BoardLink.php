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
use Organisation\Entity\Board;
use Organisation\Entity\Organisation;

/**
 * Class BoardLink
 * @package Organisation\View\Helper
 */
final class BoardLink extends AbstractLink
{
    public function __invoke(
        Board $board = null,
        string $action = 'view',
        string $show = 'name',
        Organisation $organisation = null
    ): string {
        $board ??= new Board();

        $routeParams = [];
        $showOptions = [];
        if (! $board->isEmpty()) {
            $routeParams['id']   = $board->getId();
            $showOptions['name'] = $board->getOrganisation()->parseFullName();
        }

        if (null !== $organisation) {
            $routeParams['organisationId'] = $organisation->getId();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fas fa-plus',
                    'route' => 'zfcadmin/board/new',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-create-board-organisation')
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fas fa-file',
                    'route' => 'zfcadmin/board/view',
                    'text'  => $showOptions[$show]
                        ?? $board->getOrganisation()->parseFullName()
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'zfcadmin/board/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-board-organisation')
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
