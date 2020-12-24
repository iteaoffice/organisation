<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Board
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
use Laminas\Navigation\Page\Mvc;
use Organisation\Entity\Board;

/**
 * Class BoardLabel
 * @package Organisation\Navigation\Invokable
 */
final class BoardLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Board::class)) {
            /** @var Board $board */
            $board = $this->getEntities()->get(Board::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $board->getId(),
                    ]
                )
            );

            $label = (string)$board;
        }

        if (null === $page->getLabel()) {
            $page->set('label', $label);
        }
    }
}
