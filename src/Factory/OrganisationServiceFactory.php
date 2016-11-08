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

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Organisation\Service\OrganisationService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class OrganisationServiceFactory
 *
 * @package Organisation\Factory
 */
final class OrganisationServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return OrganisationService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var OrganisationService $organisationService */
        $organisationService = new $requestedName($options);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $organisationService->setEntityManager($entityManager);

        return $organisationService;
    }
}
