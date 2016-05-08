<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Core\MessageQueue\Job;
use BrainExe\Core\Traits\JsonSerializableTrait;

/**
 * @api
 */
class JobEvent extends AbstractEvent implements PushViaWebsocket
{
    use JsonSerializableTrait;

    const ADDED   = 'message_queue.added';
    const HANDLED = 'message_queue.handled';

    /**
     * @var Job
     */
    private $job;

    /**
     * @param string $type
     * @param Job $job
     */
    public function __construct(string $type, Job $job)
    {
        parent::__construct($type);
        $this->job  = $job;
    }

    /**
     * @return Job
     */
    public function getJob() : Job
    {
        return $this->job;
    }
}
