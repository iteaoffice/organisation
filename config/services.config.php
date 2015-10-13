<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */
use Organisation\Form;

return [
    'factories' => [
        'organisation_organisation_form' => function ($sm) {
            return new Form\Organisation($sm);
        },
        'organisation_financial_form'    => function ($sm) {
            return new Form\Financial($sm);
        },
    ],
];
