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
use Organisation\Acl\Assertion\Parent\FinancialAssertion;
use Organisation\Entity;

/**
 * Class FinancialLink
 * @package Organisation\View\Helper\Parent
 */
final class FinancialLink extends AbstractLink
{
    public function __invoke(
        Entity\Parent\Financial $financial = null,
        string $action = 'view',
        string $show = 'text',
        Entity\ParentEntity $parent = null
    ): string {
        $financial ??= new Entity\Parent\Financial();

        if (! $this->hasAccess($financial, FinancialAssertion::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];
        if (! $financial->isEmpty()) {
            $routeParams['id'] = $financial->getId();
            $showOptions['organisation'] = $financial->getOrganisation()->getOrganisation();
        }

        if (null !== $parent) {
            $routeParams['parentId'] = $parent->getId();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/parent/financial/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-financial-organisation')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/parent/financial/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-financial-organisation')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
