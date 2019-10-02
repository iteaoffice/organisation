<?php
/**
 * ITEA Office all rights reserved
 *
 * @category  Content
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license   https://itea3.org/license.txt proprietary
 *
 * @link      https://itea3.org
 */

declare(strict_types=1);

namespace Organisation\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Organisation\Service\FormService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FormServiceFactory
 *
 * @package Admin\Factory
 */
final class FormServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormService
    {
        /** @var FormService $formService */
        return new $requestedName($container, $container->get(EntityManager::class));
    }
}
