<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Parent
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Organisation\Acl\Assertion\Parent\Doa as DoaAssertion;
use Organisation\Entity\OParent;
use Organisation\Entity\Parent\Doa;

/**
 * Class ParentDoaLink
 *
 * @package Organisation\View\Helper
 */
class ParentDoaLink extends AbstractLink
{
    /**
     * @param Doa $doa
     * @param string $action
     * @param string $show
     * @param OParent $parent
     *
     * @return string
     */
    public function __invoke(Doa $doa = null, $action = 'view', $show = 'name', OParent $parent = null): string
    {
        $this->setDoa($doa);
        $this->setAction($action);
        $this->setShow($show);
        $this->setParent($parent);
        /*
         * Set the non-standard options needed to give an other link value
         */
        $this->setShowOptions(
            [
                'name' => (string) $this->getDoa(),
            ]
        );
        if (!$this->hasAccess($this->getDoa(), DoaAssertion::class, $this->getAction())) {
            return '';
        }

        $this->addRouterParam('id', $this->getDoa()->getId());
        $this->addRouterParam('parentId', $this->getParent()->getId());

        return $this->createLink();
    }

    /**
     * Extract the relevant parameters based on the action.
     *
     * @throws \Exception
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'upload':
                $this->setRouter('zfcadmin/parent/doa/upload');
                $this->setText(
                    sprintf(
                        $this->translate("txt-upload-doa-for-parent-%s-link-title"),
                        $this->getParent()->getOrganisation()
                    )
                );
                break;
            case 'download':
                $this->setRouter('zfcadmin/parent/doa/download');
                $this->setText(
                    sprintf(
                        $this->translate("txt-download-doa-for-parent-%s-link-title"),
                        $this->getDoa()->getParent()->getOrganisation()
                    )
                );
                break;
            case 'view':
                $this->setRouter('zfcadmin/parent/doa/view');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-doa-for-parent-%s-link-title"),
                        $this->getDoa()->getParent()->getOrganisation()
                    )
                );
                break;
            case 'edit':
                $this->setRouter('zfcadmin/parent/doa/edit');
                $this->setText(
                    sprintf(
                        $this->translate("txt-edit-doa-for-parent-%s-link-title"),
                        $this->getDoa()->getParent()->getOrganisation()
                    )
                );
                break;
            default:
                throw new \Exception(sprintf('%s is an incorrect action for %s', $this->getAction(), __CLASS__));
        }
    }
}
