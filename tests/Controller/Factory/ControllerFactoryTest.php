<?php /**
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
namespace OrganisationTest\Controller\Factory;

use Interop\Container\ContainerInterface;
use Organisation\Controller\Factory\ControllerFactory;
use OrganisationTest\Assets\InvokableController;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @covers Organisation\Controller\Factory\ControllerFactory
 */
class ControllerFactoryTest extends TestCase
{
    public function testCanCreateService()
    {
        /** @var ServiceLocatorInterface $container */
        $container = $this->getMock(ContainerInterface::class);
        /** @var ControllerManager $controllerManager */
        $controllerManager = new ControllerManager($container);

        $factory = new ControllerFactory();
        $object  = $factory->createService($controllerManager, InvokableController::class, InvokableController::class);
        $this->assertInstanceOf(InvokableController::class, $object);
    }


}