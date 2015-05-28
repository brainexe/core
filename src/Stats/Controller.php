<?php

namespace BrainExe\Core\Stats;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\MessageQueue\Gateway as MessageQueueGateway;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Stats.Controller")
 */
class Controller
{

    use EventDispatcherTrait;

    /**
     * @var MessageQueueGateway
     */
    private $messageQueue;

    /**
     * @var Stats
     */
    private $stats;

    /**
     * @Inject({"@Stats.Stats", "@MessageQueue.Gateway"})
     * @param Stats $stats
     * @param MessageQueueGateway $gateway
     */
    public function __construct(Stats $stats, MessageQueueGateway $gateway)
    {
        $this->messageQueue = $gateway;
        $this->stats        = $stats;
    }

    /**
     * @Route("/stats/", name="status.index")
     */
    public function index()
    {
        $stats = $this->stats->getAll();

        $stats = array_merge($stats, [
            'message_queue:queued' => $this->messageQueue->countJobs(),
        ]);

        return [
            'jobs' => $this->messageQueue->getEventsByType(),
            'stats' => $stats,
        ];
    }

    /**
     * @Route("/stats/event/", methods="DELETE")
     * @param Request $request
     * @return bool
     */
    public function deleteJob(Request $request)
    {
        $jobId = $request->query->get('job_id');
        $this->messageQueue->deleteEvent($jobId);

        return true;
    }
    /**
     * @Route("/stats/reset/", methods="POST")
     * @param Request $request
     * @return bool
     */
    public function resetStats(Request $request)
    {
        $key = $request->request->get('key');
        $this->stats->set($key, 0);

        return true;
    }
}
