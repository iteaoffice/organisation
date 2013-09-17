<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
use Organisation\Form;

return array(
    'factories' => array(
        'organisation_organisation_form' => function ($sm) {
            return new Form\CreateOrganisation($sm);
        },
    ),
);
