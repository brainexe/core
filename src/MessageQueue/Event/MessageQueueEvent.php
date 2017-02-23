<?php

namespace BrainExe\Core\MessageQueue\Event;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\MessageQueue\Job;

/**
 * @api
 */
abstract class MessageQueueEvent extends AbstractEvent
{

    /**
     * @var AbstractEvent
     */
    public $event;

    /**
     * @var Job
     */
    private $job;

    /**
     * @return AbstractEvent
     */
    public function getEvent() : AbstractEvent
    {
        return $this->event;
    }

    /**
     * @return Job
     */
    public function getJob() : ?Job
    {
        return $this->job;
    }

    /**
     * @param Job $job
     */
    public function setJob(Job $job)
    {
        $this->job = $job;
    }
}
