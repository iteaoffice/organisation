<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\View\Helper;

use Organisation\Service;

/**
 * Create a link to an organisation.
 *
 * @category    Organisation
 */
class OrganisationLogo extends ImageAbstract
{
    /**
     * @param Service\OrganisationService $organisationService
     * @param null                        $class
     *
     * @return string
     */
    public function __invoke(
        Service\OrganisationService $organisationService = null,
        $class = null
    ) {
        if (is_null($organisationService)) {
            return '';
        }

        $logo = $organisationService->getOrganisation()->getLogo();
        if ($logo->count() === 0) {
            return '';
        }

        /*
         * The company can have multiple logo's. We now take just the first one.
         *
         * @var Logo
         */
        $logo = $organisationService->getOrganisation()->getLogo()->first();

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
