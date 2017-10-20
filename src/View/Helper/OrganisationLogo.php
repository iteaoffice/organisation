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
 * @link        http://github.com/iteaoffice/Organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Organisation\Entity\Organisation;

/**
 * Class OrganisationLogo
 * @package Organisation\View\Helper
 */
class OrganisationLogo extends ImageAbstract
{
    /**
     * @param Organisation $organisation
     * @param null $width
     * @param bool $onlyUrl
     * @param bool $responsive
     * @param array $classes
     * @return string
     */
    public function __invoke(
        Organisation $organisation,
        $width = null,
        $onlyUrl = false,
        $responsive = true,
        $classes = []
    ): string {
        $logo = $organisation->getLogo()->first();

        if (!$logo) {
            return '';
        }

        $this->setRouter('image/organisation-logo');

        $this->addRouterParam('ext', $logo->getContentType()->getExtension());
        $this->addRouterParam('last-update', $logo->getDateUpdated()->getTimestamp());
        $this->addRouterParam('id', $logo->getId());

        $this->setImageId('organisation_logo_' . $logo->getId());

        $this->setWidth($width);

        return $this->createImageUrl($onlyUrl);
    }
}
