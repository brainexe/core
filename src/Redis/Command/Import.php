<?php

namespace BrainExe\Core\Redis\Command;

use BrainExe\Core\Traits\RedisTrait;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use BrainExe\Core\Annotations\Command as CommandAnnotation;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @CommandAnnotation
 * @codeCoverageIgnore
 */
class Import extends SymfonyCommand
{

    use RedisTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('redis:import')
            ->setDescription('Import Redis database')
            ->addArgument('file', InputArgument::OPTIONAL, 'File to export', 'database.txt')
            ->addOption('flush', null, InputOption::VALUE_NONE, 'Flush the current database before import');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file    = $input->getArgument('file');
        $content = file_get_contents($file);

        if ($input->getOption('flush')) {
            $this->getRedis()->flushdb();
        }

        $this->handleImport($content);
    }

    /**
     * @param string $content
     */
    protected function handleImport(string $content)
    {
        $redis   = $this->getRedis();

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
