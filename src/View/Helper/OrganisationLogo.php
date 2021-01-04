<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Organisation\View\Helper;

use General\ValueObject\Image\Image;
use General\ValueObject\Image\ImageDecoration;
use General\View\Helper\AbstractImage;
use Organisation\Entity\Logo;
use Organisation\Entity\Organisation;

/**
 * Class ChallengeIcon
 *
 * @package Challenge\View\Helper
 */
final class OrganisationLogo extends AbstractImage
{
    public function __invoke(
        Organisation $organisation,
        int $width = null,
        string $show = ImageDecoration::SHOW_IMAGE
    ): string {
        /** @var Logo $logo */
        $logo = $organisation->getLogo()->first();

        if (! $logo) {
            return '';
        }

        $linkParams = [];
        $linkParams['route'] = 'image/organisation-logo';
        $linkParams['show'] = $show;
        $linkParams['width'] = $width;

        $routeParams = [
            'id' => $logo->getId(),
            'ext' => $logo->getContentType()->getExtension(),
            'last-update' => $logo->getDateUpdated()->getTimestamp(),
        ];

        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Image::fromArray($linkParams));
    }
}
