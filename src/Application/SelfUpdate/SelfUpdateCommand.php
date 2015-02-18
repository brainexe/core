<?php

namespace BrainExe\Core\Application\SelfUpdate;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Command;
use BrainExe\Core\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @Command
 */
class SelfUpdateCommand extends AbstractCommand
{

    /**
     * @var SelfUpdate
     */
    private $selfUpdate;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('self_update')
            ->setDescription('Start Self Update');
    }

    /**
     * @Inject({"@SelfUpdate", "@EventDispatcher"})
     * @param SelfUpdate $selfUpdate
     * @param EventDispatcher $dispatcher
     */
    public function __construct(SelfUpdate $selfUpdate, EventDispatcher $dispatcher)
    {
        parent::__construct();

        $this->selfUpdate = $selfUpdate;
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * @{inheritdoc}
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed|void
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $this->eventDispatcher->addListener(SelfUpdateEvent::PROCESS, function(SelfUpdateEvent $event) use ($output) {
            $output->write($event->payload);
        });

        $this->eventDispatcher->addListener(SelfUpdateEvent::ERROR, function(SelfUpdateEvent $event) use ($output) {
            $output->writeln(sprintf('<error>Error during update: %s</error>', $event->payload));
        });

        $this->selfUpdate->startUpdate();
    }
}
