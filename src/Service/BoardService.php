<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Service;

use Organisation\Entity;

/**
 * Class BoardService
 * @package Organisation\Service
 */
class BoardService extends AbstractService
{
    public function findBoardById(int $id): ?Entity\Organisation
    {
        return $this->entityManager->getRepository(Entity\Organisation::class)->find($id);
    }
}
