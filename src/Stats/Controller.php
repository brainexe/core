<?php

namespace BrainExe\Core\Stats;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Traits\TimeTrait;
use BrainExe\MessageQueue\Gateway as MessageQueueGateway;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\MessageQueue\Job;
use Predis\PredisException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Stats.Controller")
 */
class Controller
{

    use EventDispatcherTrait;
    use RedisTrait;
    use TimeTrait;

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
            'message_queue:queued' => $this->messageQueue->countAllJobs(),
        ]);

        try {
            $redisStats = $this->getRedis()->info();
        } catch (PredisException $e) {
            $redisStats = [];
        }

        return [
            'jobs'  => iterator_to_array($this->messageQueue->getEventsByType()),
            'stats' => $stats,
            'redis' => $redisStats
        ];
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

        return iterator_to_array($this->messageQueue->getEventsByType($type, $since));
    }

    /**
     * @Route("/stats/event/", methods="DELETE", name="stats.deleteJob")
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
     * @Route("/stats/reset/", methods="POST", name="stats.reset")
     * @param Request $request
     * @return bool
     */
    public function resetStats(Request $request)
    {
        $key = $request->request->get('key');
        $this->stats->set([$key => 0]);

        return true;
    }
}
