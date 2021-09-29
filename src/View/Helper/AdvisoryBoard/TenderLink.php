<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\View\Helper\AdvisoryBoard;

use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;
use Organisation\Acl\Assertion\AdvisoryBoard\TenderAssertion;
use Organisation\Entity\AdvisoryBoard\Tender;

/**
 *
 */
final class TenderLink extends AbstractLink
{
    public function __invoke(
        Tender $tender = null,
        string $action = 'view',
        string $show = 'name'
    ): string {
        $tender ??= new Tender();

        if (! $this->hasAccess($tender, TenderAssertion::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];

        if (! $tender->isEmpty()) {
            $routeParams['id']   = $tender->getId();
            $showOptions['name'] = (string)$tender;
        }

        switch ($action) {
            case 'new-admin':
                $linkParams = [
                    'icon'  => 'fas fa-plus',
                    'route' => 'zfcadmin/advisory-board/tender/new',
                    'text'  => $this->translator->translate('txt-new-tender-for-advisory-board')
                ];
                break;
            case 'view-admin':
                $linkParams = [
                    'icon'  => 'far fa-file',
                    'route' => 'zfcadmin/advisory-board/tender/details/general',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-view-tender-for-advisory-board')
                ];
                break;
            case 'edit-admin':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'zfcadmin/advisory-board/tender/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-tender-for-advisory-board')
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
