<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\Options;

/**
 * Interface ParentOptionsInterface
 * @package Organisation\Options
 */
interface ParentOptionsInterface
{
    /**
     * returns the location to the Application Form Template
     *
     * @return string
     */
    public function getApplicationFormTemplate();

    /**
     * returns the location to the Application Form Template
     *
     * @param  string $applicationFormTemplate
     *
     * @return string
     */
    public function setApplicationFormTemplate($applicationFormTemplate);
}
