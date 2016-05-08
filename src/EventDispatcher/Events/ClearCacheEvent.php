<?php

namespace BrainExe\Core\EventDispatcher\Events;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheEvent extends AbstractEvent
{

    const NAME = 'cache.clear';

    /**
     * @var OutputInterface
     */
    public $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output = null)
    {
        parent::__construct(self::NAME);

        $this->output = $output;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }
}
