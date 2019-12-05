<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Organisation\Entity\Type;
use Zend\Navigation\Page\Mvc;

/**
 * Class TypeLabel
 *
 * @package Organisation\Navigation\Invokable
 */
final class TypeLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Type::class)) {
            /** @var Type $organisation */
            $organisationType = $this->getEntities()->get(Type::class);

            $label = (string)$organisationType;
        }
        $page->set('label', $label);
    }
}
