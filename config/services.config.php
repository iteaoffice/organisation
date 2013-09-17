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
        'organisation_organisation_form'       => function ($sm) {
            return new Form\CreateOrganisation($sm);
        },
        'organisation_facility_form'      => function ($sm) {
            return new Form\CreateFacility($sm);
        },
        'organisation_area_form'          => function ($sm) {
            return new Form\CreateArea($sm);
        },
        'organisation_area2_form'         => function ($sm) {
            return new Form\CreateArea2($sm);
        },
        'organisation_sub_area_form'      => function ($sm) {
            return new Form\CreateSubArea($sm);
        },
        'organisation_oper_area_form'     => function ($sm) {
            return new Form\CreateOperArea($sm);
        },
        'organisation_oper_sub_area_form' => function ($sm) {
            return new Form\CreateOperSubArea($sm);
        },
        'organisation_message_form'       => function ($sm) {
            return new Form\CreateMessage($sm);
        },
    ),
);
