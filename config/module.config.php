<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
$config = array(
    'controllers'     => array(
        'invokables' => array(
            'organisation-index'   => 'Organisation\Controller\OrganisationController',
            'organisation-manager' => 'Organisation\Controller\OrganisationManagerController',
        ),
    ),
    'view_manager'    => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
    ),
    'service_manager' => array(
        'factories'  => array(
            'organisation-assertion' => 'Organisation\Acl\Assertion\Organisation',
        ),
        'invokables' => array(
            'organisation_organisation_service'     => 'Organisation\Service\OrganisationService',
            'organisation_form_service'             => 'Organisation\Service\FormService',
            'organisation_organisation_form_filter' => 'Organisation\Form\FilterCreateOrganisation',

        )
    ),
    'doctrine'        => array(
        'driver'       => array(
            'organisation_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    __DIR__ . '/../src/Organisation/Entity/'
                )
            ),
            'orm_default'                    => array(
                'drivers' => array(
                    'Organisation\Entity' => 'organisation_annotation_driver',
                )
            )
        ),
        'eventmanager' => array(
            'orm_default' => array(
                'subscribers' => array(
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                )
            ),
        ),
    )
);

$configFiles = array(
    __DIR__ . '/module.config.routes.php',
    __DIR__ . '/module.config.navigation.php',
    __DIR__ . '/module.config.authorize.php',
);

foreach ($configFiles as $configFile) {
    $config = Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
}

return $config;
