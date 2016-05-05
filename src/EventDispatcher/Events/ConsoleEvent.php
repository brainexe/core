<?php

namespace BrainExe\Core\EventDispatcher\Events;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleEvent extends AbstractEvent
{

    const NAME = 'console.run';

    /**
     * @var string
     */
    private $command;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param string $command
     * @param OutputInterface $output
     */
    public function __construct(
        string $command,
        OutputInterface $output = null
    ) {
        parent::__construct(self::NAME);

        $this->command = $command;
        $this->output  = $output;
    }

    /**
     * @return string
     */
    public function getCommand() : string
    {
        return $this->command;
    }

    /**
     * @return OutputInterface|null
     */
    public function getOutput()
    {
        return $this->output;
    }
}
