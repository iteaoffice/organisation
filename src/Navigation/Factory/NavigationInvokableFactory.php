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
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */

namespace Organisation\Navigation\Factory;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class NavigationInvokableFactory
 *
 * @package Organisation\Navigation\Factory
 */
final class NavigationInvokableFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return AbstractNavigationInvokable
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var $invokable AbstractNavigationInvokable */
        return new $requestedName($container, $options);
    }
}
