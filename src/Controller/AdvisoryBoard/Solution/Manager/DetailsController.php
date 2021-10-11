<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\AdvisoryBoard\Solution\Manager;

use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Service\AdvisoryBoard\SolutionService;
use Organisation\Service\FormService;

final class DetailsController extends AbstractController
{
    private SolutionService $solutionService;

    public function __construct(SolutionService $solutionService)
    {
        $this->solutionService = $solutionService;
    }

    public function generalAction(): ViewModel
    {
        $solution = $this->solutionService->findSolutionById((int)$this->params('id'));

        if (null === $solution) {
            return $this->notFoundAction();
        }

        return new ViewModel(['solution' => $solution]);
    }
}
