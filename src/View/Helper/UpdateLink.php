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
use Organisation\Acl\Assertion\UpdateAssertion;
use Organisation\Entity\Organisation;
use Organisation\Entity\Update;

/**
 * Class UpdateLink
 * @package General\View\Helper
 */
final class UpdateLink extends AbstractLink
{
    public function __invoke(
        Update $update = null,
        string $action = 'view',
        string $show = 'name',
        Organisation $organisation = null
    ): string
    {
        $update ??= new Update();

        $routeParams = [];
        $showOptions = [];
        if (!$update->isEmpty()) {
            $routeParams['id']   = $update->getId();
            $showOptions['name'] = $update->getOrganisation()->parseFullName();
        }

        if ($update->isEmpty() && null !== $organisation) {
            $update->setOrganisation($organisation);
            $routeParams['organisationId'] = $organisation->getId();
        }

        if (!$this->hasAccess($update, UpdateAssertion::class, $action)) {
            return '';
        }

        switch ($action) {
            case 'edit-admin':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'zfcadmin/organisation/update/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-update')
                ];
                break;
            case 'view-admin':
                $linkParams = [
                    'icon'  => 'fas fa-link',
                    'route' => 'zfcadmin/organisation/update/view',
                    'text'  => $showOptions[$show] ?? $update->getOrganisation()
                ];
                break;
            case 'approve':
                $linkParams = [
                    'icon'  => 'far fa-thumbs-up',
                    'route' => 'zfcadmin/organisation/update/approve',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-approve-update')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'community/organisation/update',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-update-organisation')
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
