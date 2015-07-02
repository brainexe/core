<?php

namespace BrainExe\Core\Redis\Command;

use Exception;
use Predis\Client;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @api
 */
interface MigrationInterface
{
    /**
     * @param Client $client
     * @param OutputInterface $output
     * @throws Exception
     */
    public function execute(Client $client, OutputInterface $output);
}
