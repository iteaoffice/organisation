<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Partner
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/partner for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Navigation\Invokable\Parent;

use General\Navigation\Invokable\AbstractNavigationInvokable;
use Organisation\Entity\OParent;
use Organisation\Entity\Parent\Doa;
use Laminas\Navigation\Page\Mvc;

/**
 * Class PartnerDoaLabel
 *
 * @package Partner\Navigation\Invokable
 */
final class DoaLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Doa::class)) {
            /** @var Doa $doa */
            $doa = $this->getEntities()->get(Doa::class);
            $this->getEntities()->set(OParent::class, $doa->getParent());

            $label = (string)$doa;
        }
        $page->set('label', $label);
    }
}
