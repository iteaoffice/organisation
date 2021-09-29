<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Organisation\View\Helper\AdvisoryBoard;

use General\ValueObject\Image\Image;
use General\ValueObject\Image\ImageDecoration;
use General\View\Helper\AbstractImage;
use Organisation\Entity\AdvisoryBoard\City;

/**
 *
 */
final class CityImage extends AbstractImage
{
    public function __invoke(
        City $city,
        int $width = null,
        string $show = ImageDecoration::SHOW_IMAGE
    ): string {
        /** @var City\Image $image */
        $image = $city->getImage();

        if (null === $image) {
            return '';
        }

        $linkParams          = [];
        $linkParams['route'] = 'image/advisory-board/city-image';
        $linkParams['show']  = $show;
        $linkParams['width'] = $width;

        $routeParams = [
            'id'          => $image->getId(),
            'ext'         => $image->getContentType()->getExtension(),
            'last-update' => $image->getDateUpdated()->getTimestamp(),
        ];

        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Image::fromArray($linkParams));
    }
}
