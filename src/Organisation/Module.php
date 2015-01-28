<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    SoloDB
 * @package     Organisation
 * @subpackage  Module
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 * @version     4.0
 */
namespace Organisation;

use Organisation\Service\FormServiceAwareInterface;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature;

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
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__.'/../../autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__.'/../../src/'.__NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__.'/../../config/module.config.php';
    }

    /**
     * Go to the service configuration
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return include __DIR__.'/../../config/services.config.php';
    }

    /**
     * Go to the service configuration
     *
     * @return array
     */
    public function getViewHelperConfig()
    {
        return include __DIR__.'/../../config/viewhelpers.config.php';
    }

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return array(
            'initializers' => array(
                function ($instance, $sm) {
                    if ($instance instanceof FormServiceAwareInterface) {
                        $sm          = $sm->getServiceLocator();
                        $formService = $sm->get('organisation_form_service');
                        $instance->setFormService($formService);
                    }
                },
            ),
        );
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     *
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        // TODO: Implement onBootstrap() method.
    }
}
