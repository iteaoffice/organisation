<?php
/**
 * Organisation Options
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
$options = [
    'country_color'                                 => '#00a651',
    'country_color_faded'                           => '#005C00',
    'overview_variable_contribution_template'       => __DIR__ . '/../../../../styles/' . ITEAOFFICE_HOST
        . '/template/pdf/aeneas-template.pdf',
    'overview_extra_variable_contribution_template' => __DIR__ . '/../../../../styles/' . ITEAOFFICE_HOST
        . '/template/pdf/aeneas-template.pdf',
];

/**
 * You do not need to edit below this line
 */
return [
    'organisation_option' => $options,
];
