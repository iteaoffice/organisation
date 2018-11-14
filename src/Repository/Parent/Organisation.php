<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Repository\Parent;

use Doctrine\ORM\EntityRepository;
use Organisation\Entity;

/**
 * Class Organisation
 *
 * @package Organisation\Repository\Parent
 */
final class Organisation extends EntityRepository
{
    public function findParentOrganisationByNameLike(string $name)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent_organisation');
        $queryBuilder->from(Entity\Parent\Organisation::class, 'organisation_entity_parent_organisation');
        $queryBuilder->join('organisation_entity_parent_organisation.organisation', 'organisation_entity_organisation');
        $queryBuilder->andWhere($queryBuilder->expr()->like('organisation_entity_organisation.organisation', ':like'));
        $queryBuilder->setParameter('like', sprintf("%%%s%%", $name));
        $queryBuilder->addOrderBy('organisation_entity_organisation.organisation', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }
}
