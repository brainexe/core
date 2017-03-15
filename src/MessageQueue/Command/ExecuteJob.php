<?php

namespace BrainExe\Core\MessageQueue\Command;

use BrainExe\Core\MessageQueue\Job;
use BrainExe\Core\MessageQueue\Worker;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation
 */
class ExecuteJob extends Command
{

    /**
     * @var Worker
     */
    private $worker;

    /**
     * @param Worker $worker
     */
    public function __construct(Worker $worker)
    {
        $this->worker = $worker;

        parent::__construct(null);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('messagequeue:execute')
            ->setDescription('Runs message queue job')
            ->addArgument('job', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = $input->getArgument('job');
        if (strpos($data, '#') !== false) {
            list (, $data) = explode('#', $data, 2);
        }

        $raw = @base64_decode($data);
        $job = @unserialize($raw);

        if (!$job instanceof Job) {
            throw new Exception(sprintf('Invalid job: %s', $input->getArgument('job')));
        }

        $this->worker->executeJob($job);
    }
}
