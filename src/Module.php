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

namespace Organisation;

use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature;

/**
 * Class Module
 *
 * @package Organisation
 */
class Module implements Feature\ConfigProviderInterface, Feature\ConsoleUsageProviderInterface
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
