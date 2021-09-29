<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\AdvisoryBoard\City\Manager;

use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Service\AdvisoryBoard\CityService;
use Organisation\Service\FormService;

final class DetailsController extends AbstractController
{
    private CityService $cityService;

    public function __construct(CityService $cityService, FormService $formService)
    {
        $this->cityService = $cityService;
    }

    public function generalAction(): ViewModel
    {
        $city = $this->cityService->findCityById((int)$this->params('id'));

        if (null === $city) {
            return $this->notFoundAction();
        }

        return new ViewModel(['city' => $city]);
    }
}
