<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Organisation\Service;

use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for building the cache storage
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 */
class CacheFactory implements FactoryInterface
{
    /**
     * Create a cache
     *
     * @param   ServiceLocatorInterface $serviceLocator
     *
     * @return  StorageInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $options = $serviceLocator->get('organisation_module_config');

        return StorageFactory::factory($options['cache_options']);
    }
}
