<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Organisation\Entity;
use Organisation\Repository;

/**
 * Class SelectionService
 *
 * @package Organisation\Service
 */
class SelectionService extends AbstractService
{
    public function canDeleteSelection(Entity\Selection $selection): bool
    {
        $cannotRemoveSelection = [];

        return count($cannotRemoveSelection) === 0;
    }

    public function findSelectionById(int $id): ?Entity\Selection
    {
        return $this->entityManager->getRepository(Entity\Selection::class)->find($id);
    }

    public function findOrganisationsInSelection(Entity\Selection $selection, bool $toArray = false): array
    {
        $repository = $this->entityManager->getRepository(Entity\Organisation::class);

        return $repository->findOrganisationsBySelection($selection, $toArray);
    }

    public function getAmountOfOrganisations(Entity\Selection $selection): int
    {
        try {
            $repository = $this->entityManager->getRepository(Entity\Selection::class);

            return $repository->findAmountOfOrganisationsInSelection($selection);
        } catch (DBALException $e) {
            return 0;
        }
    }

    public function findTags(): array
    {
        /** @var Repository\SelectionRepository $repository */
        $repository = $this->entityManager->getRepository(Entity\Selection::class);

        return $repository->findTags();
    }

    public function findSelectionsByOrganisation(Entity\Organisation $organisation): array
    {
        $selections = [];

        /**
         * @var $selection Entity\Selection
         */
        foreach ($this->findAll(Entity\Selection::class) as $selection) {
            if ($this->organisationInSelection($organisation, $selection)) {
                $selections[] = $selection;
            }
        }

        ksort($selections);

        return $selections;
    }

    public function organisationInSelection(Entity\Organisation $organisation, $selections): bool
    {
        if (! is_array($selections) && ! $selections instanceof PersistentCollection) {
            $selections = [$selections];
        }
        foreach ($selections as $selection) {
            if (! $selection instanceof Entity\Selection) {
                throw new \InvalidArgumentException('Selection should be instance of Selection');
            }
            if (null === $selection->getId()) {
                throw new \InvalidArgumentException('The given selection cannot be empty');
            }
            if ($this->findOrganisationInSelection($organisation, $selection)) {
                return true;
            }
        }

        return false;
    }

    public function findOrganisationInSelection(Entity\Organisation $organisation, Entity\Selection $selection): bool
    {
        $repository = $this->entityManager->getRepository(Entity\Organisation::class);

        try {
            //We have a dynamic query, check if the organisation is in the selection
            return $repository->isOrganisationInSelection($organisation, $selection);
        } catch (\Throwable $e) {
            print sprintf('Selection %s is giving troubles (%s)', $selection->getId(), $e->getMessage());
        }

        return false;
    }

    public function duplicateSelection(Entity\Selection $selection, Entity\Selection $source): void
    {
        $this->save($selection);
    }
}
