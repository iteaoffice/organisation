<?php

/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Organisation\View\Helper;

use Zend\View\Helper\AbstractHelper;

use Organisation\Service;

/**
 * Create a link to an organisation
 *
 * @category    Organisation
 * @package     View
 * @subpackage  Helper
 */
class OrganisationLogo extends AbstractHelper
{

    /**
     * @param Service\OrganisationService $organisationService
     * @param null                        $class
     *
     * @return string
     */
    public function __invoke(Service\OrganisationService $organisationService = null, $class = null)
    {
        $url  = $this->getView()->plugin('url');
        $logo = $organisationService->getOrganisation()->getLogo();

        if ($logo->count() === 0) {
            return 'no logo';
        }

        /**
         * The company can have multiple logo's. We now take just the first one
         */
        $logo   = $organisationService->getOrganisation()->getLogo()->first();
        $router = 'assets/organisation-logo';

        $classes   = array();
        $classes[] = $class;

        $imageUrl = '<img src="%s" id="%s" class="%s">';

        $params = array(
            'ext' => $logo->getContentType()->getExtension(),
            'id'  => $logo->getId()
        );


        $image = sprintf(
            $imageUrl,
            $url($router, $params),
            'organisation_logo_' . $organisationService->getOrganisation()->getId(),
            implode(' ', $classes)
        );

        return $image;
    }
}
