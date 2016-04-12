<?php

namespace BrainExe\Core\MessageQueue;

use BrainExe\Core\EventDispatcher\AbstractEvent;

/**
 * @api can be used by other components
 */
class Job
{

    /**
     * @var AbstractEvent
     */
    public $event;

    /**
     * @var string
     */
    public $jobId;

    /**
     * @var integer
     */
    public $timestamp;

    /**
     * @var int
     */
    public $errorCounter = 0;

    /**
     * @param AbstractEvent $event
     * @param string $jobId
     * @param integer $timestamp
     */
    public function __construct(AbstractEvent $event, string $jobId, int $timestamp)
    {
        $this->event     = $event;
        $this->jobId     = $jobId;
        $this->timestamp = $timestamp;
    }
}
