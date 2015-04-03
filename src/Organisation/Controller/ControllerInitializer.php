<?php
/**
 * Japaveh Webdesign copyright message placeholder.
 *
 * @category    Controller
 *
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 *
 * @link        http://solodb.net
 */

namespace Organisation\Controller;

use Organisation\Service\FormService;
use Organisation\Service\FormServiceAwareInterface;
use Organisation\Service\OrganisationService;
use Organisation\Service\OrganisationServiceAwareInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Japaveh Webdesign copyright message placeholder.
 *
 * @category    Controller
 *
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 *
 * @link        http://solodb.net
 */
class ControllerInitializer implements InitializerInterface
{
    /**
     * @param string $instance
     * @param ServiceLocatorInterface|ControllerManager $controllerManager
     *
     * @return null
     */
    public function initialize($instance, ServiceLocatorInterface $controllerManager)
    {
        if (!is_object($instance)) {
            return;
        }
        $arrayCheck = [
            OrganisationServiceAwareInterface::class => OrganisationService::class,
            FormServiceAwareInterface::class         => FormService::class,
        ];
        /*
         * @var $controllerManager ControllerManager
         */
        $sm = $controllerManager->getServiceLocator();
        /*
         * Go over each interface to see if we should add an interface
         */
        foreach (class_implements($instance) as $interface) {
            if (array_key_exists($interface, $arrayCheck)) {
                $this->setInterface($instance, $interface, $sm->get($arrayCheck[$interface]));
            }
        }

        return;
    }

    /**
     * @param $interface
     * @param $instance
     * @param $service
     */
    protected function setInterface($instance, $interface, $service)
    {
        foreach (get_class_methods($interface) as $setter) {
            if (strpos($setter, 'set') !== false) {
                $instance->$setter($service);
            }
        }
    }
}
