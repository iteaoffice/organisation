<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Organisation\Entity\Organisation;
use Project\Entity\Logo;

/**
 * Class OrganisationLogo
 * @package Organisation\View\Helper
 */
class OrganisationLogo extends ImageAbstract
{
    /**
     * @param Organisation $organisation
     * @param null $class
     * @param bool $silent
     * @param null $width
     * @return string
     */
    public function __invoke(
        Organisation $organisation,
        $class = null,
        $silent = true,
        $width = null
    ): string {
        $logos = $organisation->getLogo();
        if ($logos->isEmpty()) {
            return $silent ? '' : $this->translate('txt-no-logo-available');
        }

        /**
         * The company can have multiple logo's. We now take just the first one.
         *
         * @var $logo Logo
         */
        $logo = $logos->first();

        /*
         * Reset the classes
         */
        $this->setClasses([]);

        $this->setRouter('assets/organisation-logo');
        $this->addClasses('img-responsive');

        $this->setImageId('organisation_logo_' . $logo->getId());
        $this->addRouterParam('hash', $logo->getHash());
        $this->addRouterParam('ext', $logo->getContentType()->getExtension());
        $this->addRouterParam('id', $logo->getId());

        if (!is_null($width)) {
            $this->addRouterParam('width', $width);
        }

        return $this->createImageUrl();
    }
}
