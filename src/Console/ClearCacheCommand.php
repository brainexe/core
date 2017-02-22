<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\DependencyInjection\Rebuild;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("ClearCacheCommand")
 */
class ClearCacheCommand extends Command
{

    /**
     * @var Rebuild
     */
    private $rebuild;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cache:clear')
            ->setDescription('Clears the local cache')
            ->setAliases(['cc']);
    }

    /**
     * @param Rebuild $rebuild
     * @param EventDispatcher $dispatcher
     */
    public function __construct(Rebuild $rebuild, EventDispatcher $dispatcher)
    {
        parent::__construct();

        $this->rebuild    = $rebuild;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Rebuild DIC...');
        $this->rebuild->buildContainer();

        $event = new ClearCacheEvent();
        $this->dispatcher->dispatchEvent($event);

        @mkdir(ROOT . 'logs', 0744);
        @mkdir(ROOT . 'cache', 0744);

        $output->writeln('<info>done</info>');
    }
}
