<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Parent
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/partner for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Navigation\Invokable;

use General\Navigation\Invokable\AbstractNavigationInvokable;
use Organisation\Entity\OParent;
use Laminas\Navigation\Page\Mvc;

/**
 * Class PartnerLabel
 *
 * @package Partner\Navigation\Invokable
 */
final class ParentLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(OParent::class)) {
            /** @var OParent $parent */
            $parent = $this->getEntities()->get(OParent::class);

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
        $page->set('label', $label);
    }
}
