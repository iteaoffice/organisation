<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Organisation\Acl\Assertion\OParent as ParentAssertion;
use Organisation\Entity;
use Organisation\Entity\Organisation;

/**
 * Class ParentLink
 *
 * @package Organisation\View\Helper
 */
class ParentLink extends AbstractLink
{
    /**
     * @param Entity\OParent|null $parent
     * @param string $action
     * @param string $show
     * @param Organisation|null $organisation
     * @param int|null $year
     * @return string
     */
    public function __invoke(
        Entity\OParent $parent = null,
        $action = 'view',
        $show = 'text',
        Organisation $organisation = null,
        int $year = null
    ): string {
        $this->setParent($parent);
        $this->setAction($action);
        $this->setShow($show);
        $this->setOrganisation($organisation);
        $this->setYear($year);

        if (!$this->hasAccess($this->getParent(), ParentAssertion::class, $this->getAction())) {
            return '';
        }

        $this->addRouterParam('id', $this->getParent()->getId());
        $this->addRouterParam('organisationId', $this->getOrganisation()->getId());
        $this->addRouterParam('year', $this->getYear());

        $this->setShowOptions(
            [
                'parent' => $this->getParent(),
            ]
        );

        return $this->createLink();
    }

    /**
     * Parse the action.
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/parent/new');
                $this->setText($this->translate('txt-new-parent'));
                break;
            case 'import-parent':
                $this->setRouter('zfcadmin/parent/import/parent');
                $this->setText($this->translate('txt-import-parent'));
                break;
            case 'import-project':
                $this->setRouter('zfcadmin/parent/import/project');
                $this->setText($this->translate('txt-import-project'));
                break;
            case 'create-from-organisation':
                $this->setRouter('zfcadmin/parent/new');
                $this->setText(sprintf($this->translate('txt-new-parent-from-%s'), $this->getOrganisation()));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/parent/edit');
                $this->setText(sprintf($this->translate('txt-edit-parent-%s'), $this->getParent()));
                break;
            case 'add-organisation':
                $this->setRouter('zfcadmin/parent/add-organisation');
                $this->setText(sprintf($this->translate('txt-add-organisation-to-parent-%s'), $this->getParent()));
                break;
            case 'overview-variable-contribution':
                $this->setRouter('zfcadmin/parent/overview-variable-contribution');
                $this->setText(
                    sprintf(
                        $this->translate('txt-overview-variable-contribution-for-parent-in-%s'),
                        $this->getYear()
                    )
                );
                break;
            case 'overview-variable-contribution-pdf':
                $this->setRouter('zfcadmin/parent/overview-variable-contribution-pdf');
                $this->setText(
                    sprintf(
                        $this->translate('txt-overview-variable-contribution-for-parent-in-%s-pdf'),
                        $this->getYear()
                    )
                );
                break;
            case 'overview-extra-variable-contribution':
                $this->setRouter('zfcadmin/parent/overview-extra-variable-contribution');
                $this->setText(
                    sprintf(
                        $this->translate('txt-overview-extra-variable-contribution-for-parent-in-%s'),
                        $this->getYear()
                    )
                );
                break;
            case 'overview-extra-variable-contribution-pdf':
                $this->setRouter('zfcadmin/parent/overview-extra-variable-contribution-pdf');
                $this->setText(
                    sprintf(
                        $this->translate('txt-overview-extra-variable-contribution-for-parent-in-%s-pdf'),
                        $this->getYear()
                    )
                );
                break;
            case 'list':
                $this->setRouter('zfcadmin/parent/list');
                $this->setText($this->translate('txt-list-parents'));
                break;
            case 'view':
                $this->setRouter('zfcadmin/parent/view');
                $this->setText(sprintf($this->translate('txt-view-parent-%s'), $this->getParent()));
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        '%s is an incorrect action for %s',
                        $this->getAction(),
                        __CLASS__
                    )
                );
        }
    }
}
