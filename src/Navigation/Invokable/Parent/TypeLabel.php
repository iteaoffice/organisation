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

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Organisation\Entity\Parent\Type;
use Zend\Navigation\Page\Mvc;

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
