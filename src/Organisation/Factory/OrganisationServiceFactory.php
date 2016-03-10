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
use Organisation\Service\OrganisationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class OrganisationServiceFactory
 *
 * @package Organisation\Factory
 */
class OrganisationServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OrganisationService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $organisationService = new OrganisationService();

        /** @var EntityManager $entityManager */
        $entityManager = $serviceLocator->get(EntityManager::class);
        $organisationService->setEntityManager($entityManager);

        return $organisationService;
    }
}
