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
use Organisation\Acl\Assertion\Parent\OrganisationAssertion;
use Organisation\Entity\Parent\Organisation;

/**
 * Class OrganisationLink
 * @package Organisation\View\Helper\Parent
 */
final class OrganisationLink extends AbstractLink
{
    public function __invoke(
        Organisation $organisation,
        string $action = 'view',
        string $show = 'text'
    ): string {
        if (! $this->hasAccess($organisation, OrganisationAssertion::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];

        $routeParams['id'] = $organisation->getId();
        $showOptions['organisation'] = (string)$organisation->getOrganisation();
        $showOptions['member-type'] = (string)$organisation->getParent()->getType();
        $showOptions['member-status'] = $this->translator->translate($organisation->getParent()->getMemberType(true));

        switch ($action) {
            case 'add-affiliation':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/parent/organisation/add-affiliation',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-parent-organisation-add-affiliation')
                ];
                break;
            case 'merge':
                $linkParams = [
                    'icon' => 'fas fa-compress-alt',
                    'route' => 'zfcadmin/parent/organisation/merge',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-merge-parent-organisation')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/parent/organisation/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-parent-organisation')
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'fas fa-link',
                    'route' => 'zfcadmin/parent/organisation/view',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-view-parent-organisation')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
