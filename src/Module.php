<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation;

use Laminas\Console\Adapter\AdapterInterface;
use Laminas\ModuleManager\Feature;

/**
 * Class Module
 *
 * @package Organisation
 */
final class Module implements Feature\ConfigProviderInterface, Feature\ConsoleUsageProviderInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getConsoleUsage(AdapterInterface $console): array
    {
        return [
            'Organisation management',
            // Describe available commands
            'organisation cleanup' => 'Perform a cleanup of to be empty organisations',
        ];
    }
}
