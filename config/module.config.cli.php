<?php

/**
 * Jield BV all rights reserved
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2021 Jield BV (https://jield.nl)
 */

declare(strict_types=1);

namespace Organisation;

return [
    'laminas-cli' => [
        'commands' => [
            'organisation:cleanup' => Command\Cleanup::class,
        ],
    ],
];
