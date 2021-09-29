<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\AdvisoryBoard\Tender\Manager;

use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Service\AdvisoryBoard\TenderService;
use Organisation\Service\FormService;

final class DetailsController extends AbstractController
{
    private TenderService $tenderService;

    public function __construct(TenderService $tenderService, FormService $formService)
    {
        $this->tenderService = $tenderService;
    }

    public function generalAction(): ViewModel
    {
        $tender = $this->tenderService->findTenderById((int)$this->params('id'));

        if (null === $tender) {
            return $this->notFoundAction();
        }

        return new ViewModel(['tender' => $tender]);
    }
}
