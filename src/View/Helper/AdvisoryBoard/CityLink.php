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
use Organisation\Acl\Assertion\AdvisoryBoard\CityAssertion;
use Organisation\Entity\AdvisoryBoard\City;

/**
 *
 */
final class CityLink extends AbstractLink
{
    public function __invoke(
        City $city = null,
        string $action = 'view',
        string $show = 'name'
    ): string {
        $city ??= new City();

        if (! $this->hasAccess($city, CityAssertion::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];

        if (! $city->isEmpty()) {
            $routeParams['id']   = $city->getId();
            $showOptions['name'] = (string)$city;
        }

        switch ($action) {
            case 'new-admin':
                $linkParams = [
                    'icon'  => 'fas fa-plus',
                    'route' => 'zfcadmin/advisory-board/city/new',
                    'text'  => $this->translator->translate('txt-new-city-for-advisory-board')
                ];
                break;
            case 'view-admin':
                $linkParams = [
                    'icon'  => 'far fa-file',
                    'route' => 'zfcadmin/advisory-board/city/details/general',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-view-city-for-advisory-board')
                ];
                break;
            case 'edit-admin':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'zfcadmin/advisory-board/city/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-city-for-advisory-board')
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
