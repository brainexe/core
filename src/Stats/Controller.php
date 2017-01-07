<?php

namespace BrainExe\Core\Stats;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\MessageQueue\Gateway as MessageQueueGateway;
use BrainExe\Core\Traits\RedisTrait;
use Predis\PredisException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Stats.Controller")
 */
class Controller
{
    use RedisTrait;

    /**
     * @var MessageQueueGateway
     */
    private $messageQueue;

    /**
     * @var Stats
     */
    private $stats;

    /**
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
    public function index() : array
    {
        $stats = $this->stats->getAll();

        try {
            $redisStats = $this->getRedis()->info();
        } catch (PredisException $e) {
            $redisStats = [];
        }

        return [
            'jobs'  => $this->messageQueue->getEventsByType(),
            'stats' => $stats,
            'redis' => $redisStats
        ];
    }

    /**
     * @Route("/stats/reset/", methods="POST", name="stats.reset")
     * @param Request $request
     * @return bool
     */
    public function resetStats(Request $request) : bool
    {
        $key = $request->request->get('key');
        $this->stats->set([$key => 0]);

        return true;
    }
}
