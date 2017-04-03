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

namespace Organisation\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Organisation\Entity\Type;
use Zend\Navigation\Page\Mvc;

/**
 * Class TypeLabel
 *
 * @package Organisation\Navigation\Invokable
 */
class TypeLabel extends AbstractNavigationInvokable
{
    /**
     * @param Mvc $page
     *
     * @return void
     */
    public function __invoke(Mvc $page)
    {
        if ($this->getEntities()->containsKey(Type::class)) {
            /** @var Type $organisation */
            $organisationType = $this->getEntities()->get(Type::class);

            $label = (string)$organisationType;
        } else {
            $label = $this->translate('txt-nav-view');
        }
        $page->set('label', $label);
    }
}
