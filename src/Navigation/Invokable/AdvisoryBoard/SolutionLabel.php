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
 * Class SolutionLabel
 * @package Organisation\Navigation\Invokable
 */
final class SolutionLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(AdvisoryBoard\Solution::class)) {
            /** @var AdvisoryBoard\Solution $solution */
            $solution = $this->getEntities()->get(AdvisoryBoard\Solution::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $solution->getId(),
                    ]
                )
            );

            $label = (string)$solution;
        }
        if (null === $page->getLabel()) {
            $page->set('label', $label);
        }
    }
}
