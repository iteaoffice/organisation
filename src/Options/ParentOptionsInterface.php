<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

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
