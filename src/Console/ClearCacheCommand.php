<?php

namespace BrainExe\Core\Console;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\DependencyInjection\Rebuild;
use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("ClearCacheCommand")
 */
class ClearCacheCommand extends Command
{

    use EventDispatcherTrait;

    /**
     * @var Rebuild
     */
    private $rebuild;

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
     * @Inject
     * @param Rebuild $rebuild
     */
    public function __construct(Rebuild $rebuild)
    {
        $this->rebuild = $rebuild;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Rebuild DIC...');
        $this->rebuild->buildContainer();

        $event = new ClearCacheEvent();
        $this->dispatchEvent($event);

        $output->writeln('<info>done</info>');
    }
}
