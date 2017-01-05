<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Organisation\View\Helper;

use Organisation\Entity\Organisation;
use Project\Entity\Logo;

/**
 * Create a link to an organisation.
 *
 * @category    Organisation
 */
class OrganisationLogo extends ImageAbstract
{
    /**
     * @param Organisation $organisation
     * @param null         $class
     *
     * @return string
     */
    public function __invoke(
        Organisation $organisation,
        $class = null
    ) {
        $logo = $organisation->getLogo();
        if ($logo->count() === 0) {
            return '';
        }

        /**
         * The company can have multiple logo's. We now take just the first one.
         *
         * @var $logo Logo
         */
        $logo = $logo->first();

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

        return $this->createImageUrl();
    }
}
