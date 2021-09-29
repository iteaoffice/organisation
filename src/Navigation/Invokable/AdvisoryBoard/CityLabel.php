<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Navigation\Invokable\AdvisoryBoard;

use General\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;
use Organisation\Entity\AdvisoryBoard;

/**
 * Class CityLabel
 * @package Organisation\Navigation\Invokable
 */
final class CityLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(AdvisoryBoard\City::class)) {
            /** @var AdvisoryBoard\City $city */
            $city = $this->getEntities()->get(AdvisoryBoard\City::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $city->getId(),
                    ]
                )
            );

            $label = (string)$city;
        }

        if (null === $page->getLabel()) {
            $page->set('label', $label);
        }
    }
}
