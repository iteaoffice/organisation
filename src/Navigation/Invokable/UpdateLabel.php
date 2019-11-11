<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Organisation\Entity\Update;
use Zend\Navigation\Page\Mvc;

/**
 * Class UpdateLabel
 *
 * @package Organisation\Navigation\Invokable
 */
final class UpdateLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Update::class)) {
            /** @var Update $update */
            $update = $this->getEntities()->get(Update::class);
            $page->setParams(array_merge($page->getParams(), ['id' => $update->getId()]));

            $label = $page->get('label');

            if (null === $label) {
                $label = (string)$update->getOrganisation();
            }
        }

        $page->set('label', $label);
    }
}
