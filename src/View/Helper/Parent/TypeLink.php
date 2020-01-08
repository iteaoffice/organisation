<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\View\Helper\Parent;

use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;
use Organisation\Entity\Parent\Type;

/**
 * Class TypeLink
 * @package Organisation\View\Helper\Parent
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
            $routeParams['id'] = $type->getId();
            $showOptions['name'] = $type->getType();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fa-plus',
                    'route' => 'zfcadmin/parent-type/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-parent-type')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/parent-type/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-parent-type')
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'fa-link',
                    'route' => 'zfcadmin/parent-type/view',
                    'text' => $showOptions[$show] ?? $type->getType()
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
