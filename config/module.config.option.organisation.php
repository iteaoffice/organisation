<?php

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
