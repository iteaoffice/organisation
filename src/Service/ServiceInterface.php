<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Service;

use Doctrine\ORM\EntityManager;
use Organisation\Entity\AbstractEntity;

/**
 * Interface ServiceInterface
 *
 * @package Organisation\Service
 */
interface ServiceInterface
{
    /**
     * @param AbstractEntity $entity
     *
     * @return AbstractEntity
     */
    public function updateEntity(AbstractEntity $entity);

    /**
     * @param AbstractEntity $entity
     *
     * @return AbstractEntity
     */
    public function newEntity(AbstractEntity $entity);

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager;

    /**
     * @param string $entity
     *
     * @return array
     */
    public function findAll(string $entity);
}
