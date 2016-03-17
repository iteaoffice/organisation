<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    SoloDB
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 *
 * @version     4.0
 */

namespace Organisation;

use Organisation\Controller\Plugin\GetFilter;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature;
use Zend\Mvc\Controller\PluginManager;

/**
 *
 */
class Module implements
    Feature\AutoloaderProviderInterface,
    Feature\ServiceProviderInterface,
    Feature\ConfigProviderInterface,
    Feature\BootstrapListenerInterface,
    Feature\ViewHelperProviderInterface
{
    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/../../autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    /**
     * Move this to here to have config cache working
     *
     * @return array
     */
    public function getControllerPluginConfig()
    {
        return [
            'factories' => [
                'getOrganisationFilter' => function (PluginManager $sm) {
                    return new GetFilter();
                },
            ]
        ];
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Go to the service configuration.
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return include __DIR__ . '/../../config/services.config.php';
    }

    /**
     * Go to the service configuration.
     *
     * @return array
     */
    public function getViewHelperConfig()
    {
        return include __DIR__ . '/../../config/viewhelpers.config.php';
    }

    /**
     * @return array
     */
    public function getControllerConfig()
    {
    }

    /**
     * Listen to the bootstrap event.
     *
     * @param EventInterface $e
     *
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
    }
}
