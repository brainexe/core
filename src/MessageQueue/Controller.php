<?php

namespace BrainExe\Core\MessageQueue;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\MessageQueue\Gateway as MessageQueueGateway;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\TimeTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("MessageQueue.Controller")
 */
class Controller
{
    use EventDispatcherTrait;
    use TimeTrait;

    /**
     * @var MessageQueueGateway
     */
    private $gateway;

    /**
     * @Inject({"@MessageQueue.Gateway"})
     * @param MessageQueueGateway $gateway
     */
    public function __construct(MessageQueueGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @Route("/jobs/{type}/", name="status.jobs.type", methods="GET")
     * @param Request $request
     * @param string $type
     * @return Job[]
     */
    public function getJobs(Request $request, $type)
    {
        $since = 0;

        if ($request->query->get('futureOnly')) {
            $since = $this->now();
        }

        return $this->gateway->getEventsByType($type, $since);
    }

    /**
     * @Route("/jobs/{jobId}/", methods="DELETE", name="messageQueue.deleteJob")
     * @param Request $request
     * @param $jobId
     * @return bool
     */
    public function deleteJob(Request $request, $jobId)
    {
        unset($request);

        $this->gateway->deleteEvent($jobId);

        return true;
    }
}
