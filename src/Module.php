<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation;

use Organisation\Controller\Plugin\GetFilter;
use Zend\ModuleManager\Feature;
use Zend\Mvc\Controller\PluginManager;

/**
 *
 */
class Module implements
    Feature\ConfigProviderInterface,
    Feature\AutoloaderProviderInterface,
    Feature\ControllerPluginProviderInterface
{
    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/../autoload_classmap.php',
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
        return include __DIR__ . '/../config/module.config.php';
    }
}
