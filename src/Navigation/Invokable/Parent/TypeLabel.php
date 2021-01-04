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
use Organisation\Entity\Parent\Type;
use Laminas\Navigation\Page\Mvc;

/**
 * Class PartnerTypeLabel
 *
 * @package Partner\Navigation\Invokable
 */
final class TypeLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Type::class)) {
            /** @var Type $type */
            $type = $this->getEntities()->get(Type::class);

            $label = (string)$type;
        }
        $page->set('label', $label);
    }
}
