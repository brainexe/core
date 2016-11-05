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
     * @var int
     */
    public $timestamp;

    /**
     * @var int
     */
    public $startTime;

    /**
     * @var int
     */
    public $errorCounter = 0;

    /**
     * @param AbstractEvent $event
     * @param string $jobId
     * @param int $timestamp
     */
    public function __construct(AbstractEvent $event, string $jobId, int $timestamp)
    {
        $this->event     = $event;
        $this->jobId     = $jobId;
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getJobId() : string
    {
        return $this->jobId;
    }

    /**
     * @return int
     */
    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

    /**
     * @return AbstractEvent
     */
    public function getEvent() : AbstractEvent
    {
        return $this->event;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp(int $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }
}
