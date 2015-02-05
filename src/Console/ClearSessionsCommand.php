<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\Authentication\Register;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Traits\RedisTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @Command
 */
class ClearSessionsCommand extends Command
{

    use RedisTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sessions:clear')
            ->setDescription('Clear all sessions');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $redis = $this->getRedis();

        $sessionIds = $redis->keys('session:*');

        foreach ($sessionIds as $sessionId) {
            $redis->del($sessionId);
        }

        $output->writeln(sprintf('Deleted <info>%d</info> sessions', count($sessionIds)));
    }
}
