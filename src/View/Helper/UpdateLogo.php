<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use General\ValueObject\Image\Image;
use General\ValueObject\Image\ImageDecoration;
use General\View\Helper\AbstractImage;
use Organisation\Entity\Update;

/**
 * Class UpdateLogo
 *
 * @package Organisation\View\Helper
 */
final class UpdateLogo extends AbstractImage
{
    public function __invoke(
        Update $update,
        int $width = null
    ): string {
        $logo = $update->getLogo();
        $route = 'image/organisation-update-logo';

        if ($logo === null) {
            $logo = $update->getOrganisation()->getLogo()->first();
            $route = 'image/organisation-logo';
        }

        if (! $logo) {
            return '';
        }

        $linkParams = [];
        $linkParams['route'] = $route;
        $linkParams['show'] = ImageDecoration::SHOW_IMAGE;
        $linkParams['width'] = $width;

        $date = $logo->getDateUpdated() ?? $logo->getDateCreated();

        $routeParams = [
            'id' => $logo->getId(),
            'ext' => $logo->getContentType()->getExtension(),
            'last-update' => null === $date ? 0 : $date->getTimestamp(),
        ];

        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Image::fromArray($linkParams));
    }
}
