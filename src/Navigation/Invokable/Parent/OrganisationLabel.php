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

namespace Organisation\Navigation\Invokable\Parent;

use General\Navigation\Invokable\AbstractNavigationInvokable;
use Organisation\Entity\OParent;
use Organisation\Entity\Parent\Organisation;
use Laminas\Navigation\Page\Mvc;

/**
 * Class OrganisationLabel
 *
 * @package Organisation\Navigation\Invokable
 */
final class OrganisationLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Organisation::class)) {
            /** @var Organisation $organisation */
            $organisation = $this->getEntities()->get(Organisation::class);

            $this->getEntities()->set(OParent::class, $organisation->getParent());
            $page->setParams(array_merge($page->getParams(), ['id' => $organisation->getId()]));
            $label = (string)$organisation;
        }
        $page->set('label', $label);
    }
}
