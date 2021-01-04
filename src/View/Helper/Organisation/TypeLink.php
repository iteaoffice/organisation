<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\View\Helper\Organisation;

use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;
use Organisation\Entity\Type;

/**
 * Class TypeLink
 * @package Organisation\View\Helper
 */
final class TypeLink extends AbstractLink
{
    public function __invoke(
        Type $type = null,
        string $action = 'view',
        string $show = 'name'
    ): string {
        $type ??= new Type();

        $routeParams = [];
        $showOptions = [];
        if (! $type->isEmpty()) {
            $routeParams['id']          = $type->getId();
            $showOptions['name']        = $type->getType();
            $showOptions['description'] = $type->getDescription();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fas fa-plus',
                    'route' => 'zfcadmin/organisation/type/new',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-organisation-type')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'zfcadmin/organisation/type/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-organisation-type')
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fas fa-link',
                    'route' => 'zfcadmin/organisation/type/view',
                    'text'  => $showOptions[$show] ?? $type->getType()
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
