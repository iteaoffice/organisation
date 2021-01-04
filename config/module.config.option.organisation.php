<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

/**
 * Organisation Options
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */

$options = [
    'overview_variable_contribution_template'       => __DIR__ . '/../../../../styles/' . (\defined('ITEAOFFICE_HOST') ? ITEAOFFICE_HOST : 'test')
        . '/template/pdf/aeneas-template.pdf',
    'overview_extra_variable_contribution_template' => __DIR__ . '/../../../../styles/' . (\defined('ITEAOFFICE_HOST') ? ITEAOFFICE_HOST : 'test')
        . '/template/pdf/aeneas-template.pdf',
];

/**
 * You do not need to edit below this line
 */
return [
    'organisation_option' => $options,
];
