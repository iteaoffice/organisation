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
use Organisation\Acl\Assertion\AdvisoryBoard\SolutionAssertion;
use Organisation\Entity\AdvisoryBoard\Solution;

/**
 *
 */
final class SolutionLink extends AbstractLink
{
    public function __invoke(
        Solution $solution = null,
        string $action = 'view',
        string $show = 'name'
    ): string {
        $solution ??= new Solution();

        if (! $this->hasAccess($solution, SolutionAssertion::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];

        if (! $solution->isEmpty()) {
            $routeParams['id']   = $solution->getId();
            $showOptions['name'] = (string)$solution;
        }

        switch ($action) {
            case 'new-admin':
                $linkParams = [
                    'icon'  => 'fas fa-plus',
                    'route' => 'zfcadmin/advisory-board/solution/new',
                    'text'  => $this->translator->translate('txt-new-solution-for-advisory-board')
                ];
                break;
            case 'view-admin':
                $linkParams = [
                    'icon'  => 'far fa-file',
                    'route' => 'zfcadmin/advisory-board/solution/details/general',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-view-solution-for-advisory-board')
                ];
                break;
            case 'edit-admin':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'zfcadmin/advisory-board/solution/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-solution-for-advisory-board')
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
