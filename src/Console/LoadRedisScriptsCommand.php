<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\Redis\RedisScripts;
use BrainExe\Core\Traits\RedisTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class LoadRedisScriptsCommand extends AbstractCommand
{

    use RedisTrait;

    /**
     * @var RedisScripts
     */
    private $redisScripts;

    /**
     * @Inject({"@RedisScripts"})
     * @param RedisScripts $redisScripts
     */
    public function __construct(RedisScripts $redisScripts)
    {
        parent::__construct();

        $this->redisScripts = $redisScripts;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('redis:scripts:load')
            ->setDescription('Load Redis Scrips');
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $redis = $this->getRedis();

        foreach ($this->redisScripts->getAllScripts() as $sha1 => $script) {
            if ($redis->script('EXISTS', $sha1)[0]) {
                if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                    $output->writeln(sprintf("Script %s was already loaded", $sha1));
                }
            } else {
                if (!$redis->script('LOAD', $script)) {
                    $output->writeln(sprintf('<error>Error: %s</error>', $redis->getLastError()));
                    $output->writeln(sprintf('<error>%s</error>', $script));
                } elseif (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                    $output->writeln(sprintf("Loaded script %s (%s)", $sha1, $script));
                }
            }
        }
    }
}
