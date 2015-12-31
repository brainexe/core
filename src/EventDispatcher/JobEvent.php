<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Core\MessageQueue\Job;

/**
 * @api
 */
class JobEvent extends AbstractEvent implements PushViaWebsocket
{

    const ADDED   = 'message_queue.added';
    const HANDLED = 'message_queue.handled';

    /**
     * @var Job
     */
    public $job;

    /**
     * @param string $type
     * @param Job $job
     */
    public function __construct($type, Job $job)
    {
        parent::__construct($type);
        $this->job  = $job;
    }
}
