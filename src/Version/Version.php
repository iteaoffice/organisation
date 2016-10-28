<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\Version;

use Zend\Http;
use Zend\Json\Json;

/**
 * Class to store and retrieve the version of Organisation module.
 */
final class Version
{
    /**
     * Zend Framework version identification - see compareVersion().
     */
    const VERSION = '2.0.0';
}
