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
     * ConsoleCacheEvent constructor.
     * @param string $command
     * @param string $arguments
     */
    public function __construct($command, $arguments = '')
    {
        parent::__construct(self::NAME);

        $this->command   = $command;
        $this->arguments = $arguments;
    }
}
