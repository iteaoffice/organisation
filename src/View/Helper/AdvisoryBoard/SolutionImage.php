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
use Organisation\Entity\AdvisoryBoard\Solution;

/**
 *
 */
final class SolutionImage extends AbstractImage
{
    public function __invoke(
        Solution $solution,
        int $width = null,
        string $show = ImageDecoration::SHOW_IMAGE
    ): string {
        /** @var Solution\Image $image */
        $image = $solution->getImage();

        if (null === $image) {
            return '';
        }

        $linkParams          = [];
        $linkParams['route'] = 'image/advisory-board/solution-image';
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
