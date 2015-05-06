<?php

namespace BrainExe\Core\Redis\Command;

use BrainExe\Core\Traits\RedisTrait;
use Symfony\Component\Console\Command\Command;
use BrainExe\Core\Annotations\Command as CommandAnnotation;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @CommandAnnotation("Redis.Command.Import")
 * @codeCoverageIgnore
 */
class Import extends Command
{

    use RedisTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('redis:import')
             ->setDescription('Import Redis database')
             ->addArgument('file', InputArgument::OPTIONAL, 'File to export', 'database.txt');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $redis   = $this->getRedis();
        $file    = $input->getArgument('file');
        $content = file_get_contents($file);

        foreach (explode("\n", $content) as $line) {
            preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $line, $matches);
            if (empty($matches[0])) {
                continue;
            }

            foreach ($matches[0] as &$value) {
                $value = trim($value, '"');
                $value = str_replace('\"', '"', $value);
            }

            $redis->executeRaw($matches[0]);
        }
    }
}
