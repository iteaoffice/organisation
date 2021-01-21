<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Navigation\Invokable;

use General\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;
use Organisation\Entity\ParentEntity;

/**
 * Class ParentLabel
 * @package Organisation\Navigation\Invokable
 */
final class ParentLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(ParentEntity::class)) {
            /** @var ParentEntity $parent */
            $parent = $this->getEntities()->get(ParentEntity::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $parent->getId(),
                    ]
                )
            );

            $label = (string)$parent;
        }

        if (null === $page->getLabel()) {
            $page->set('label', $label);
        }
    }
}
