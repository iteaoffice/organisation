<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Navigation\Invokable\Parent;

use General\Navigation\Invokable\AbstractNavigationInvokable;
use Organisation\Entity\OParent;
use Organisation\Entity\Parent\Financial;
use Laminas\Navigation\Page\Mvc;

/**
 * Class OrganisationLabel
 *
 * @package Organisation\Navigation\Invokable
 */
final class FinancialLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Financial::class)) {
            /** @var Financial $financial */
            $financial = $this->getEntities()->get(Financial::class);

            $this->getEntities()->set(OParent::class, $financial->getParent());
            $page->setParams(array_merge($page->getParams(), ['id' => $financial->getId()]));
            $label = (string)$financial;
        }
        $page->set('label', $label);
    }
}
