<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/main for the canonical source repository
 */
namespace Organisation\Factory;

use Interop\Container\ContainerInterface;
use Organisation\Options\ModuleOptions;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ModuleOptionsFactory
 *
 * @package Organisation\Factory
 */
class ModuleOptionsFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null|null    $options
     *
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ModuleOptions
    {
        $config = $container->get('Config');

        return new ModuleOptions(isset($config['organisation_option']) ? $config['organisation_option'] : []);
    }
}
