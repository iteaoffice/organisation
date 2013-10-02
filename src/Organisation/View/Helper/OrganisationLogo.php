<?php

/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Organisation
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
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
     *
     * @return string
     */
    public function __invoke(Service\OrganisationService $organisationService = null)
    {
        $url  = $this->getView()->plugin('url');
        $logo = $organisationService->getOrganisation()->getLogo();

        if (is_null($logo)) {
            return 'no logo';
        }
var_dump($logo->count());
        $logo = current($logo);

        /**
         * Check if the file is cached and if so, pull it from the assets-folder
         */
        $router = 'organisation/logo';

        if (file_exists($logo->getCacheFileName())) {
            /**
             * The file exists, but is it not updated?
             */
            if ($logo->getDateUpdated()->getTimestamp() > filemtime($logo->getCacheFileName())) {
                unlink($logo->getCacheFileName());
            } else {
                $router = 'assets/organisation-logo';
            }
        } else {
            file_put_contents(
                $logo->getCacheFileName(),
                is_resource($logo->getLogo()) ? stream_get_contents($logo->getLogo()) : $logo->getLogo()
            );
        }

        $imageUrl = '<img src="%s?%s" id="%s">';

        $params = array(
            'hash' => $logo->getHash(),
            'ext'  => $logo->getContentType()->getExtension(),
            'id'   => $logo->getOrganisation()->getId()
        );

        $image = sprintf(
            $imageUrl,
            $url($router, $params),
            $logo->getDateUpdated()->getTimestamp(),
            'organisation_logo_' . $organisationService->getOrganisation()->getId()
        );

        return $image;
    }
}
