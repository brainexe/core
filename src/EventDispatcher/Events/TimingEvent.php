<?php

namespace BrainExe\Core\EventDispatcher\Events;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class TimingEvent extends AbstractEvent {

    const TIMING_EVENT = 'timing';

    /**
     * @var string
     */
    public $timingId;

    /**
     * @param string $timingId
     */
    public function __construct($timingId)
    {
        parent::__construct(self::TIMING_EVENT);

        $this->timingId = $timingId;
    }
}
