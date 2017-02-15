<?php

namespace BrainExe\Core\MessageQueue;


use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\MessageQueue\Gateway as MessageQueueGateway;
use BrainExe\Core\Traits\TimeTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("MessageQueue.Controller")
 */
class Controller
{
    use TimeTrait;

    /**
     * @var MessageQueueGateway
     */
    private $gateway;

    /**
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
    public function getJobs(Request $request, string $type) : array
    {
        $since = 0;

        if ($request->query->getInt('futureOnly')) {
            $since = $this->now();
        }

        return $this->gateway->getEventsByType($type, $since);
    }

    /**
     * @Route("/jobs/{eventId}:{jobId}/", methods="DELETE", name="messageQueue.deleteJob", requirements={"jobId":"\d+"})
     * @param Request $request
     * @param string $eventType
     * @param int $jobId
     * @return bool
     */
    public function deleteJob(Request $request, string $eventType, int $jobId) : bool
    {
        unset($request);

        return $this->gateway->deleteEvent($jobId, $eventType);
    }
}
