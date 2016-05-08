<?php

namespace BrainExe\Core\EventDispatcher\Events;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\Traits\JsonSerializableTrait;
use JsonSerializable;

class TimingEvent extends AbstractEvent implements JsonSerializable
{
    use JsonSerializableTrait;

    const TIMING_EVENT = 'timing';

    /**
     * @var string
     */
    private $timingId;

    /**
     * @param string $timingId
     */
    public function __construct(string $timingId)
    {
        parent::__construct(self::TIMING_EVENT);

        $this->timingId = $timingId;
    }

    /**
     * @return string
     */
    public function getTimingId()
    {
        return $this->timingId;
    }
}
