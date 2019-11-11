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
use Organisation\Entity\Update;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;

/**
 * Class UpdateLogo
 *
 * @package Organisation\View\Helper
 */
final class UpdateLogo extends ImageAbstract
{
    public function __invoke(
        Update $update,
        int    $width = null
    ): string {
        $logo = $update->getLogo();

        $prefix = 'organisation_update_logo_';
        $this->setRouter('image/organisation-update-logo');
        if ($logo === null) {
            $logo = $update->getOrganisation()->getLogo()->first();
            $prefix = 'organisation_logo_';
            $this->setRouter('image/organisation-logo');
        }

        if (!$logo) {
            return '';
        }

        $this->classes = [];

        $this->addRouterParam('ext', $logo->getContentType()->getExtension());
        $date = $logo->getDateUpdated() ?? $logo->getDateCreated();
        $this->addRouterParam('last-update', $date->getTimestamp());
        $this->addRouterParam('id', $logo->getId());

        $this->setImageId($prefix . $logo->getId());

        $this->setWidth($width);

        return $this->createImageUrl();
    }
}
