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
use Organisation\Entity\Organisation;
use Organisation\Entity\Selection;

/**
 * Class SelectionLink
 * @package General\View\Helper
 */
final class SelectionLink extends AbstractLink
{
    public function __invoke(
        Selection $selection = null,
        string $action = 'view',
        string $show = 'name',
        Organisation $organisation = null
    ): string {
        $selection ??= new Selection();

        $routeParams = [];
        $showOptions = [];
        if (! $selection->isEmpty()) {
            $routeParams['id']        = $selection->getId();
            $showOptions['selection'] = $selection->getSelection();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fas fa-plus',
                    'route' => 'zfcadmin/organisation/selection/new',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-selection')
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fas fa-link',
                    'route' => 'zfcadmin/organisation/selection/view',
                    'text'  => $showOptions[$show] ?? $selection->getSelection()
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'zfcadmin/organisation/selection/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-selection')
                ];
                break;
            case 'copy':
                $linkParams = [
                    'icon'  => 'far fa-clone',
                    'route' => 'zfcadmin/organisation/selection/copy',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-copy-selection')
                ];
                break;
            case 'edit-sql':
                $linkParams = [
                    'icon'  => 'fas fa-code',
                    'route' => 'zfcadmin/organisation/selection/edit-sql',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-sql')
                ];
                break;
            case 'export-csv':
                $routeParams['type'] = 'csv';
                $linkParams          = [
                    'icon'  => 'far fa-file-alt',
                    'route' => 'zfcadmin/organisation/selection/export',
                    'text'  => $showOptions[$show] ?? $this->translator->translate('txt-export-organisation-selection-to-csv')
                ];
                break;
            case 'export-excel':
                $routeParams['type'] = 'excel';
                $linkParams          = [
                    'icon'  => 'far fa-file-excel',
                    'route' => 'zfcadmin/organisation/selection/export',
                    'text'  => $showOptions[$show] ?? $this->translator->translate('txt-export-organisation-selection-to-excel')
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
