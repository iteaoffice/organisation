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
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

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
     * @param string              $action
     * @param string              $show
     * @param Organisation|null   $organisation
     * @param null                $year
     * @param null                $period
     *
     * @return string
     */
    public function __invoke(
        Entity\OParent $parent = null,
        $action = 'view',
        $show = 'text',
        Organisation $organisation = null,
        $year = null,
        $period = null
    ): string {
        $this->setParent($parent);
        $this->setAction($action);
        $this->setShow($show);
        $this->setOrganisation($organisation);
        $this->setYear($year);
        $this->setPeriod($period);

        if (! $this->hasAccess($this->getParent(), ParentAssertion::class, $this->getAction())) {
            return '';
        }

        $this->addRouterParam('id', $this->getParent()->getId());
        $this->addRouterParam('organisationId', $this->getOrganisation()->getId());
        $this->addRouterParam('year', $year);
        $this->addRouterParam('period', $period);

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
    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/parent/new');
                $this->setText($this->translate('txt-new-parent'));
                break;
            case 'import':
                $this->setRouter('zfcadmin/parent/import');
                $this->setText($this->translate('txt-import-parent'));
                break;
            case 'create-from-organisation':
                $this->setRouter('zfcadmin/parent/new');
                $this->setText(sprintf($this->translate('txt-new-parent-from-%s'), $this->getOrganisation()));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/parent/edit');
                $this->setText(sprintf($this->translate('txt-edit-parent-%s'), $this->getParent()));
                break;
            case 'edit-financial':
                $this->setRouter('zfcadmin/parent/edit-financial');
                $this->setText(sprintf($this->translate('txt-edit-financial-%s'), $this->getParent()));
                break;
            case 'add-organisation':
                $this->setRouter('zfcadmin/parent/add-organisation');
                $this->setText(sprintf($this->translate('txt-add-organisation-to-parent-%s'), $this->getParent()));
                break;
            case 'overview-variable-contribution':
                $this->setRouter('zfcadmin/parent/overview-variable-contribution');
                $this->setText(
                    sprintf(
                        $this->translate('txt-overview-variable-contribution-for-parent-in-%s-%s'),
                        $this->getYear(),
                        $this->getPeriod()
                    )
                );
                break;
            case 'overview-variable-contribution-pdf':
                $this->setRouter('zfcadmin/parent/overview-variable-contribution-pdf');
                $this->setText(
                    sprintf(
                        $this->translate('txt-overview-variable-contribution-for-parent-in-%s-%s-pdf'),
                        $this->getYear(),
                        $this->getPeriod()
                    )
                );
                break;
            case 'overview-extra-variable-contribution':
                $this->setRouter('zfcadmin/parent/overview-extra-variable-contribution');
                $this->setText(
                    sprintf(
                        $this->translate('txt-overview-extra-variable-contribution-for-parent-in-%s-%s'),
                        $this->getYear(),
                        $this->getPeriod()
                    )
                );
                break;
            case 'overview-extra-variable-contribution-pdf':
                $this->setRouter('zfcadmin/parent/overview-extra-variable-contribution-pdf');
                $this->setText(
                    sprintf(
                        $this->translate('txt-overview-extra-variable-contribution-for-parent-in-%s-%s-pdf'),
                        $this->getYear(),
                        $this->getPeriod()
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
            case 'view-public':
                $this->addRouterParam('docRef', $this->getParent()->getId());
                $this->setRouter('route-parent_entity_parent');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-parent-%s"),
                        $this->getParent()
                    )
                );
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
