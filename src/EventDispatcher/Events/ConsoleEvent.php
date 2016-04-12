<?php

namespace BrainExe\Core\EventDispatcher\Events;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class ConsoleEvent extends AbstractEvent
{

    const NAME = 'console.run';

    /**
     * @var string
     */
    public $command;

    /**
     * @var string
     */
    public $arguments;

    /**
     * @param string $command
     * @param string $arguments
     */
    public function __construct(string $command, string $arguments = '')
    {
        parent::__construct(self::NAME);

        $this->command   = $command;
        $this->arguments = $arguments;
    }
}
