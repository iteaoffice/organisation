<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Partner
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/partner for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Navigation\Invokable\Parent;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Organisation\Entity\Parent\Status;
use Zend\Navigation\Page\Mvc;

/**
 * Class PartnerStatusLabel
 *
 * @package Partner\Navigation\Invokable
 */
class StatusLabel extends AbstractNavigationInvokable
{
    /**
     * @param Mvc $page
     *
     * @return void
     */
    public function __invoke(Mvc $page): void
    {
        if ($this->getEntities()->containsKey(Status::class)) {
            /** @var Status $status */
            $status = $this->getEntities()->get(Status::class);

            $label = (string)$status;
        } else {
            $label = $this->translate('txt-nav-view');
        }
        $page->set('label', $label);
    }
}
