<?php

namespace BrainExe\Core\MessageQueue;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Traits\TimeTrait;
use Generator;

/**
 * @api
 * @Service("MessageQueue.Gateway", public=false)
 */
class Gateway
{

    use TimeTrait;
    use RedisTrait;
    use IdGeneratorTrait;

    const QUEUE_DELAYED   = 'message_queue:delayed';
    const QUEUE_IMMEDIATE = 'message_queue:immediate';
    const META_DATA       = 'message_queue:meta_data';
    const RETRY_TIME      = 3600; // try again after 1 hour

    /**
     * @param string $eventId
     * @param string $eventType
     * @return bool success
     */
    public function deleteEvent(string $eventId, string $eventType = null) : bool
    {
        $eventId = sprintf('%s:%s', $eventType, $eventId);

        $redis = $this->getRedis();
        $delayed = $redis->zrem(self::QUEUE_DELAYED, $eventId);
        if ($delayed) {
            $redis->hdel(self::META_DATA, [$eventId]);
            return true;
        }

        $immediate = $this->getRedis()->lrange(self::QUEUE_IMMEDIATE, 0, 100);
        foreach ($immediate as $rawJob) {
            list($jobId) = explode('#', $rawJob, 2);
            if (strpos($jobId, "$eventId") === 0) {
                return (bool)$this->redis->lrem(self::QUEUE_IMMEDIATE, 1, $rawJob);
            }
        }

        return false;
    }

    /**
     * @param AbstractEvent $event
     * @param int $timestamp
     * @return Job
     */
    public function addEvent(AbstractEvent $event, int $timestamp = 0) : Job
    {
        $jobId = $this->generateUniqueId('jobid:' . $event->getEventName());
        $jobId = sprintf('%s:%s', $event->getEventName(), $jobId);

        $job = new Job($event, $jobId, $timestamp);
        $job->startTime = $this->now();

        $this->addJob($job);

        return $job;
    }

    /**
     * @param Job $job
     */
    public function addJob(Job $job)
    {
        $serialized = base64_encode(serialize($job));

        $pipeline = $this->getRedis()->pipeline(['fire-and-forget' => true]);
        if (empty($job->timestamp)) {
            // immediate execution in background
            $pipeline->lpush(self::QUEUE_IMMEDIATE, $job->jobId . '#' . $serialized);
        } else {
            // delayed execution
            $pipeline->hset(self::META_DATA, $job->jobId, $serialized);
            $pipeline->zadd(self::QUEUE_DELAYED, [
                $job->jobId => (int)$job->timestamp
            ]);
        }

        $pipeline->execute();
    }

    /**
     * @param string $eventType
     * @param int $since
     * @return Job[]
     */
    public function getEventsByType(string $eventType = null, int $since = 0) : array
    {
        return iterator_to_array($this->getEventsByTypeGenerator($eventType, $since));
    }

    /**
     * @param string $eventType
     * @param int $since
     * @return Generator|Job[]
     */
    public function getEventsByTypeGenerator(string $eventType = null, int $since = 0) : Generator
    {
        $redis = $this->getRedis();

        $resultRaw = $redis->zrangebyscore(
            self::QUEUE_DELAYED,
            $since,
            '+inf',
            ['withscores' => true]
        );

        $keys = [];
        foreach ($resultRaw as $jobId => $timestamp) {
            if (empty($eventType) || strpos($jobId, $eventType . ":") === 0) {
                $keys[$jobId] = $timestamp;
            }
        }

        yield from $this->getFromTimesQueue($keys);
        yield from $this->getFromImmediateQueue($eventType);
    }

    /**
     * @param Job $job
     */
    public function restoreJob(Job $job)
    {
        $now = $this->now();

        $job->timestamp = $now + self::RETRY_TIME;
        $job->errorCounter++;
        $this->addJob($job);
    }

    /**
     * @return int
     */
    public function countAllJobs() : int
    {
        $delayed   = $this->getRedis()->zcard(self::QUEUE_DELAYED);
        $immediate = $this->getRedis()->llen(self::QUEUE_IMMEDIATE);

        return $delayed + $immediate;
    }

    /**
     * @param array $keys
     * @return Generator
     */
    private function getFromTimesQueue(array $keys) : Generator
    {
        if (!empty($keys)) {
            $events = $this->getRedis()->hmget(self::META_DATA, array_keys($keys));
            foreach ($events as $jobId => $rawJob) {
                /** @var Job $job */
                $job = unserialize(base64_decode($rawJob));
                yield $job->jobId => $job;
            }
        }
    }

    /**
     * @param string $eventType
     * @return Generator
     */
    private function getFromImmediateQueue(string $eventType = null) : Generator
    {
        $immediate = $this->getRedis()->lrange(self::QUEUE_IMMEDIATE, 0, 100);
        foreach ($immediate as $rawJob) {
            list($jobId, $rawJob) = explode('#', $rawJob, 2);
            if (empty($eventType) || strpos($jobId, $eventType . ":") === 0) {
                /** @var Job $job */
                $job = unserialize(base64_decode($rawJob));
                yield $job->jobId => $job;
            }
        }
    }
}
