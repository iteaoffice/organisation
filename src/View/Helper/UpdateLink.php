<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Organisation\Entity\Organisation;
use Organisation\Entity\Update;
use Organisation\Acl\Assertion\UpdateAssertion;

/**
 * Class UpdateLink
 * @package Organisation\View\Helper
 */
class UpdateLink extends AbstractLink
{
    /**
     * @var Update
     */
    private $update;

    public function __invoke(
        Update       $update = null,
        string       $action = 'view',
        string       $show = 'name',
        Organisation $organisation = null
    ): string {
        $this->update = $update ?? new Update();
        $this->setAction($action);
        $this->setShow($show);
        $this->organisation = $organisation ?? new Organisation();
        if ($this->update->isEmpty()) {
            $this->update->setOrganisation($this->organisation);
            $this->addRouterParam('organisationId', $this->organisation->getId());
        }

        if (!$this->hasAccess($this->update, UpdateAssertion::class, $this->getAction())) {
            return '';
        }

        $this->setShowOptions([
            'name' => $this->update->getOrganisation()->parseFullName()
        ]);
        $this->addRouterParam('id', $this->update->getId());

        return $this->createLink();
    }

    /**
     * @throws \Exception
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'edit':
                $this->setRouter('community/organisation/update');
                $this->setText($this->translator->translate("txt-update-organisation"));
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }
}
