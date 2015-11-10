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

use Organisation\Entity\Logo;
use Zend\View\Helper\AbstractHelper;

/**
 * Class VersionServiceProxy.
 */
class CreateLogoFromArray extends AbstractHelper
{
    /**
     * @param array $logoDetails
     *
     * @return Logo
     */
    public function __invoke(array $logoDetails)
    {
        $Logo = new Logo();
        foreach ($logoDetails as $key => $value) {
            $Logo->$key = $value;
        }

        return $Logo;
    }
}
