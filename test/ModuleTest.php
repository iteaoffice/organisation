<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProjectTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace OrganisationTest\InputFilter;

use Organisation\Module;
use Testing\Util\AbstractServiceTest;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Zend\View\HelperPluginManager;

/**
 * Class ModuleTest
 *
 * @package OrganisationTest\InputFilter
 */
class ModuleTest extends AbstractServiceTest
{
    public function testCanFindConfiguration(): void
    {
        $module = new Module();
        $config = $module->getConfig();

        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('service_manager', $config);
        $this->assertArrayHasKey(ConfigAbstractFactory::class, $config);
    }

    /**
     *
     */
    public function testInstantiationOfConfigAbstractFactories(): void
    {
        $module = new Module();
        $config = $module->getConfig();

        $abstractFacories = $config[ConfigAbstractFactory::class] ?? [];

        foreach ($abstractFacories as $service => $dependencies) {


            $instantiatedDependencies = [];
            foreach ($dependencies as $dependency) {

                if ($dependency === 'Application') {
                    $dependency = Application::class;
                }
                if ($dependency === 'ViewHelperManager') {
                    $dependency = HelperPluginManager::class;
                }
                if ($dependency === 'Config') {
                    $dependency = [];
                }
                if ($dependency !== Application::class) {
                    $instantiatedDependencies[]
                        = $this->getMockBuilder($dependency)->disableOriginalConstructor()->getMock();

                } else {

                    $applicationMock = $this->getMockBuilder($dependency)->disableOriginalConstructor()->setMethods(
                        ['getMvcEvent']
                    )->getMock();

                    $mvcEvent = new MvcEvent();

                    $applicationMock->expects($this->any())
                        ->method('getMvcEvent')
                        ->will($this->returnValue($mvcEvent));

                    $instantiatedDependencies[] = $applicationMock;
                }
            }

            $instance = new $service(...$instantiatedDependencies);

            $this->assertInstanceOf($service, $instance);
        }

    }
}