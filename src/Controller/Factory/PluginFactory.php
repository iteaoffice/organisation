<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */
namespace Organisation\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class PluginFactory
 *
 * @package Contact\Controller\Factory
 */
final class PluginFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface|PluginManager $container
     * @param string                           $requestedName
     * @param array|null                       $options
     *
     * @return AbstractPlugin
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AbstractPlugin
    {
        /** @var AbstractPlugin $plugin */
        $plugin = new $requestedName($options);

        if (method_exists($plugin, 'setServiceLocator')) {
            $plugin->setServiceLocator($container);
        }

        return $plugin;
    }
}
