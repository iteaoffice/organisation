<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\View\Helper\Parent;

use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;
use Organisation\Acl\Assertion\OParent as ParentAssertion;
use Organisation\Entity;
use Organisation\Entity\Organisation;
use Program\Entity\Program;

/**
 * Class ParentLink
 *
 * @package Organisation\View\Helper
 */
final class ParentLink extends AbstractLink
{
    public function __invoke(
        Entity\OParent $parent = null,
        string $action = 'view',
        string $show = 'text',
        Organisation $organisation = null,
        Program $program = null,
        int $year = null
    ): string {
        $parent ??= new Entity\OParent();

        if (!$this->hasAccess($parent, ParentAssertion::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];
        if (!$parent->isEmpty()) {
            $routeParams['id'] = $parent->getId();
            $showOptions['parent'] = (string)$parent;
        }

        if (null !== $organisation) {
            $routeParams['organisationId'] = $organisation->getId();
        }
        if (null !== $year) {
            $routeParams['year'] = $year;
        }
        if (null !== $program) {
            $routeParams['program'] = $program->getId();
        }


        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/parent/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-parent')
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'fas fa-link',
                    'route' => 'zfcadmin/parent/view',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-view-parent')
                ];
                break;
            case 'import-project':
                $linkParams = [
                    'icon' => 'fas fa-upload',
                    'route' => 'zfcadmin/parent/import/project',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-import-project')
                ];
                break;
            case 'create-from-organisation':
                $linkParams = [
                    'icon' => 'fa-exchange',
                    'route' => 'zfcadmin/parent/new',
                    'text' => $showOptions[$show]
                        ?? sprintf($this->translator->translate('txt-new-parent-from-%s'), (string)$organisation)
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/parent/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-parent')
                ];
                break;
            case 'add-organisation':
                $linkParams = [
                    'icon' => 'fa-exchange',
                    'route' => 'zfcadmin/parent/add-organisation',
                    'text' => $showOptions[$show] ?? $this->translator->translate('txt-add-organisation-to-parent')
                ];

                break;
            case 'overview-variable-contribution':
                $linkParams = [
                    'icon' => 'fas fa-euro-sign',
                    'route' => 'zfcadmin/parent/overview-variable-contribution',
                    'text' => $showOptions[$show] ?? sprintf(
                        $this->translator->translate('txt-overview-variable-contribution-for-parent-in-program-%s-in-%s'),
                        (string)$program,
                        $year
                    )
                ];
                break;
            case 'overview-variable-contribution-pdf':
                $linkParams = [
                    'icon' => 'far fa-file-pdf',
                    'route' => 'zfcadmin/parent/overview-variable-contribution-pdf',
                    'text' => $showOptions[$show] ?? sprintf(
                        $this->translator->translate('txt-overview-variable-contribution-for-parent-in-program-%s-in-%s-pdf'),
                        (string)$program,
                        $year
                    )
                ];
                break;
            case 'overview-extra-variable-contribution':
                $linkParams = [
                    'icon' => 'fas fa-euro-sign',
                    'route' => 'zfcadmin/parent/overview-extra-variable-contribution',
                    'text' => $showOptions[$show] ?? sprintf(
                        $this->translator->translate('txt-overview-extra-variable-contribution-for-parent-in-program-%s-in-%s'),
                        (string)$program,
                        $year
                    )
                ];
                break;
            case 'overview-extra-variable-contribution-pdf':
                $linkParams = [
                    'icon' => 'far fa-file-pdf',
                    'route' => 'zfcadmin/parent/overview-extra-variable-contribution-pdf',
                    'text' => $showOptions[$show] ?? sprintf(
                        $this->translator->translate('txt-overview-extra-variable-contribution-for-parent-in-program-%s-in-%s-pdf'),
                        (string)$program,
                        $year
                    )
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
