<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Organisation\Service;

use Affiliation\Service\AffiliationService;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Interop\Container\ContainerInterface;
use Organisation\Entity;
use Project\Service\ProjectService;
use Project\Service\VersionService;

/**
 * AbstractService.
 */
abstract class AbstractService implements ServiceInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    /**
     * @var ContainerInterface
     */
    protected $serviceLocator;
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var AffiliationService
     */
    protected $affiliationService;
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var VersionService
     */
    protected $versionService;

    /**
     * @param      $entity
     * @param bool $toArray
     *
     * @return array
     */
    public function findAll(string $entity, bool $toArray = false)
    {
        return $this->getEntityManager()->getRepository($entity)->findAll();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     *
     * @return AbstractService
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @param string $entity
     * @param        $filter
     *
     * @return Query
     */
    public function findEntitiesFiltered($entity, $filter): Query
    {
        return $this->getEntityManager()->getRepository($entity)
                    ->findFiltered($filter, AbstractQuery::HYDRATE_SIMPLEOBJECT);
    }

    /**
     * @param $entity
     * @param $id
     *
     * @return null|object
     */
    public function findEntityById($entity, $id)
    {
        return $this->getEntityManager()->getRepository($entity)->find($id);
    }

    /**
     * @param Entity\AbstractEntity $entity
     *
     * @return Entity\AbstractEntity
     */
    public function newEntity(Entity\AbstractEntity $entity): Entity\AbstractEntity
    {
        return $this->updateEntity($entity);
    }

    /**
     * @param Entity\AbstractEntity $entity
     *
     * @return Entity\AbstractEntity
     */
    public function updateEntity(Entity\AbstractEntity $entity): Entity\AbstractEntity
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * @param Entity\AbstractEntity $entity
     *
     * @return bool
     */
    public function removeEntity(Entity\AbstractEntity $entity): bool
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService(): OrganisationService
    {
        if (is_null($this->organisationService)) {
            $this->organisationService = $this->getServiceLocator()->get(OrganisationService::class);
        }

        return $this->organisationService;
    }

    /**
     * @return ContainerInterface
     */
    public function getServiceLocator(): ContainerInterface
    {
        return $this->serviceLocator;
    }

    /**
     * @param ContainerInterface $serviceLocator
     *
     * @return AbstractService
     */
    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * @return AffiliationService
     */
    public function getAffiliationService(): AffiliationService
    {
        if (is_null($this->affiliationService)) {
            $this->affiliationService = $this->getServiceLocator()->get(AffiliationService::class);
        }

        return $this->affiliationService;
    }

    /**
     * @return VersionService
     */
    public function getVersionService(): VersionService
    {
        if (is_null($this->versionService)) {
            $this->versionService = $this->getServiceLocator()->get(VersionService::class);
        }

        return $this->versionService;
    }

    /**
     * @return ProjectService
     */
    public function getProjectService(): ProjectService
    {
        if (is_null($this->projectService)) {
            $this->projectService = $this->getServiceLocator()->get(ProjectService::class);
        }

        return $this->projectService;
    }
}
