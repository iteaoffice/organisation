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
use Zend\I18n\Translator\TranslatorInterface;
use Zend\View\Helper\AbstractHelper;
use function sprintf;

/**
 * Class UpdateNotification
 *
 * @package Organisation\View\Helper
 */
final class UpdateNotification extends AbstractHelper
{
    /**
     * @var UpdateService
     */
    private $updateService;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(UpdateService $updateService, TranslatorInterface $translator)
    {
        $this->updateService = $updateService;
        $this->translator    = $translator;
    }

    public function __invoke(Organisation $organisation): string
    {
        if ($this->updateService->hasPendingUpdates($organisation)) {
            return sprintf(
                '<span class="badge badge-info"><i class="fa fa-info-circle"></i> %s</span>',
                $this->translator->translate('txt-has-pending-updates')
            );
        }

        return '';
    }
}
