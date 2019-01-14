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
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;

/**
 * Class OrganisationLogo
 *
 * @package Organisation\View\Helper
 */
final class OrganisationLogo extends ImageAbstract
{
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

        $this->classes = [];

        $this->setRouter('image/organisation-logo');

        $this->addRouterParam('ext', $logo->getContentType()->getExtension());
        $this->addRouterParam('last-update', $logo->getDateUpdated()->getTimestamp());
        $this->addRouterParam('id', $logo->getId());

        $this->setImageId('organisation_logo_' . $logo->getId());

        $this->setWidth($width);
        if ($responsive) {
            $this->addClasses('img-fluid');
        }

        if ($onlyUrl) {
            $url = $this->getHelperPluginManager()->get(Url::class);
            $serverUrl = $this->getHelperPluginManager()->get(ServerUrl::class);

            return $serverUrl() . $url($this->router, $this->routerParams);
        }

        return $this->createImageUrl($onlyUrl);
    }
}
