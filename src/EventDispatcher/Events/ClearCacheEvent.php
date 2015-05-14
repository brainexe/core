<?php

namespace BrainExe\Core\EventDispatcher\Events;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheEvent extends AbstractEvent
{

    const NAME = 'cache.clear';

    /**
     * @var OutputInterface
     */
    public $output;

    /**
     * @var Application
     */
    public $application;

    /**
     * @var InputInterface
     */
    public $input;

    /**
     * @param Application $application
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(
        Application $application,
        InputInterface $input,
        OutputInterface $output
    ) {
        parent::__construct(self::NAME);

        $this->output      = $output;
        $this->input       = $input;
        $this->application = $application;
    }
}
