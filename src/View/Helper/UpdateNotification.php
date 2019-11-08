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
 * @link        http://github.com/iteaoffice/Organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Organisation\Entity\Organisation;
use Organisation\Service\UpdateService;
use Zend\View\Helper\AbstractHelper;

/**
 * Class UpdateNotification
 *
 * @package Organisation\View\Helper
 */
final class UpdateNotification extends AbstractHelper
{
    /**
     * @var UpdateService
     */
    private $updateService;

    /**
     * UpdateNotification constructor.
     * @param UpdateService $updateService
     */
    public function __construct(UpdateService $updateService)
    {
        $this->updateService = $updateService;
    }

    public function __invoke(Organisation $organisation): string
    {
        if ($updateS) {
        }
    }
}
