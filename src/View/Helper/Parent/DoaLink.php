<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\View\Helper\Parent;

use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;
use Organisation\Acl\Assertion\Parent\Doa as DoaAssertion;
use Organisation\Entity\OParent;
use Organisation\Entity\Parent\Doa;

/**
 * Class ParentDoaLink
 *
 * @package Organisation\View\Helper
 */
final class DoaLink extends AbstractLink
{
    public function __invoke(
        Doa $doa = null,
        string $action = 'view',
        string $show = 'name',
        OParent $parent = null
    ): string {
        $doa ??= new Doa();

        if (! $this->hasAccess($doa, DoaAssertion::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];

        if (! $doa->isEmpty()) {
            $routeParams['id'] = $doa->getId();
            $showOptions['name'] = (string)$doa;
        }

        if (null !== $parent) {
            $routeParams['parentId'] = $parent->getId();
        }

        switch ($action) {
            case 'upload':
                $linkParams = [
                    'icon' => 'fas fa-upload',
                    'route' => 'zfcadmin/parent/doa/upload',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-upload-doa-for-parent')
                ];
                break;
            case 'download':
                $linkParams = [
                    'icon' => 'fas fa-download',
                    'route' => 'zfcadmin/parent/doa/download',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-download-doa-for-parent')
                ];

                break;
            case 'view':
                $linkParams = [
                    'icon' => 'far fa-file',
                    'route' => 'zfcadmin/parent/doa/view',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-view-doa-for-parent')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/parent/doa/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-doa-for-parent')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
