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
use Organisation\Entity\ParentEntity;
use Organisation\Entity\Parent\Organisation;
use Laminas\Navigation\Page\Mvc;

/**
 * Class OrganisationLabel
 *
 * @package Organisation\Navigation\Invokable
 */
final class OrganisationLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Organisation::class)) {
            /** @var Organisation $organisation */
            $organisation = $this->getEntities()->get(Organisation::class);

            $this->getEntities()->set(ParentEntity::class, $organisation->getParent());
            $page->setParams(array_merge($page->getParams(), ['id' => $organisation->getId()]));
            $label = (string)$organisation;
        }
        $page->set('label', $label);
    }
}
