<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/Organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Organisation\Entity\Organisation;
use Organisation\Service\UpdateService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Helper\AbstractHelper;
use function sprintf;

/**
 * Class UpdateNotification
 *
 * @package Organisation\View\Helper
 */
final class UpdateNotification extends AbstractHelper
{
    private UpdateService $updateService;
    private TranslatorInterface $translator;

    public function __construct(UpdateService $updateService, TranslatorInterface $translator)
    {
        $this->updateService = $updateService;
        $this->translator = $translator;
    }

    public function __invoke(Organisation $organisation): string
    {
        if ($this->updateService->hasPendingUpdates($organisation)) {
            return sprintf(
                '<div class="alert alert-info"><i class="fa fa-info-circle"></i> %s</div>',
                $this->translator->translate('txt-your-organisation-has-pending-updates')
            );
        }

        return '';
    }
}
