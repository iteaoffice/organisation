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

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of retrieving an array containing the project configuration
 *
 * @author Johan van der Heide <johan.van.der.heide@itea3.org>
 */
class ConfigServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        return $config['organisation-config'];
    }
}
