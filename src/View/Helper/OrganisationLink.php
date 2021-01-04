<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Content\Entity\Route;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;
use Organisation\Acl\Assertion\Organisation as OrganisationAssertion;
use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;

/**
 * Class OrganisationLink
 * @package Organisation\View\Helper
 */
final class OrganisationLink extends AbstractLink
{
    public function __invoke(
        Organisation $organisation = null,
        string $action = 'view',
        string $show = 'name',
        string $branch = null
    ): string {
        $organisation ??= new Organisation();

        if (! $this->hasAccess($organisation, OrganisationAssertion::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];
        if (! $organisation->isEmpty()) {
            $routeParams['id'] = $organisation->getId();
            $routeParams['docRef'] = $organisation->getDocRef();
            $showOptions['name'] = OrganisationService::parseBranch($branch, $organisation);
            $showOptions['name-and-country'] = sprintf(
                '%s (%s)',
                OrganisationService::parseBranch($branch, $organisation),
                $organisation->getCountry(),
            );
        }


        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/organisation/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-organisation')
                ];
                break;
            case 'view-admin':
                $linkParams = [
                    'icon' => 'fas fa-link',
                    'route' => 'zfcadmin/organisation/view',
                    'text' => $showOptions[$show]
                        ?? OrganisationService::parseBranch($branch, $organisation)
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/organisation/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-organisation')
                ];
                break;
            case 'manage-web':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/organisation/manage-web',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-manage-websites')
                ];
                break;
            case 'edit-financial':
                $linkParams = [
                    'icon' => 'far fa-credit-card',
                    'route' => 'zfcadmin/organisation/financial/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-financial-organisation')
                ];

                break;
            case 'list-financial':
                $linkParams = [
                    'icon' => 'far fa-credit-card',
                    'route' => 'zfcadmin/organisation/financial/list',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-list-financial-organisations')
                ];
                break;
            case 'add-affiliation':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/organisation/add-affiliation',
                    'text' => $showOptions[$show]
                        ?? sprintf(
                            $this->translator->translate('txt-add-organisation-%s-to-project'),
                            OrganisationService::parseBranch($branch, $organisation)
                        )
                ];
                break;
            case 'view':
                $linkParams = [
                    'route' => (Route::parseRouteName(Route::DEFAULT_ROUTE_ORGANISATION)),
                    'text' => $showOptions[$show]
                        ?? OrganisationService::parseBranch($branch, $organisation)
                ];

                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
