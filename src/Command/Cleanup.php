<?php

/**
 * Jield BV all rights reserved
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2021 Jield BV (https://jield.nl)
 */

namespace Organisation\Command;

use Organisation\Service\OrganisationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
final class Cleanup extends Command
{
    /** @var string */
    protected static $defaultName = 'organisation:cleanup';
    private OrganisationService $organisationService;

    public function __construct(OrganisationService $organisationService)
    {
        parent::__construct(self::$defaultName);

        $this->organisationService = $organisationService;
    }

    protected function configure(): void
    {
        $this->setName(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start organisation cleanup');
        $this->organisationService->removeInactiveOrganisations($output);
        $output->writeln('Organisation cleanup completed');

        return Command::SUCCESS;
    }
}
