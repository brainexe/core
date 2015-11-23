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
    const RETRY_TIME      = 3600; // try again after 20 seconds

    /**
     * @param integer $eventId
     * @param null $eventType
     * @return bool success
     */
    public function deleteEvent($eventId, $eventType = null)
    {
        if ($eventType) {
            $eventId = sprintf('%s:%s', $eventType, $eventId);
        }

        $redis = $this->getRedis();
        $delayed = $redis->ZREM(self::QUEUE_DELAYED, $eventId);
        if ($delayed) {
            $redis->HDEL(self::META_DATA, $eventId);
            return true;
        }

        $immediate = $this->getRedis()->lrange(self::QUEUE_IMMEDIATE, 0, 100);
        foreach ($immediate as $rawJob) {
            list($jobId) = explode('#', $rawJob, 2);
            if (strpos($jobId, "$eventId") === 0) {
                $result = $this->redis->lrem(self::QUEUE_IMMEDIATE, 1, $rawJob);
                return (bool)$result;
            }
        }

        return false;
    }

    /**
     * @param AbstractEvent $event
     * @param integer $timestamp
     * @return string
     */
    public function addEvent(AbstractEvent $event, $timestamp = 0)
    {
        $randomId = $this->generateUniqueId();
        $jobId    = sprintf('%s:%s', $event->eventName, $randomId);

        $job = new Job($event, $jobId, $timestamp);

        $this->addJob($job);
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
            $pipeline->LPUSH(self::QUEUE_IMMEDIATE, $job->jobId . '#' . $serialized);
        } else {
            // delayed execution
            $pipeline->HSET(self::META_DATA, $job->jobId, $serialized);
            $pipeline->ZADD(self::QUEUE_DELAYED, (int)$job->timestamp, $job->jobId);
        }

        $pipeline->execute();
    }

    /**
     * @param string $eventType
     * @param int $since
     * @return Job[]
     */
    public function getEventsByType($eventType = null, $since = 0)
    {
        return iterator_to_array($this->getEventsByTypeGenerator($eventType, $since));
    }

    /**
     * @param string $eventType
     * @param integer $since
     * @return Generator|Job[]
     */
    public function getEventsByTypeGenerator($eventType = null, $since = 0)
    {
        $redis = $this->getRedis();

        $resultRaw = $redis->ZRANGEBYSCORE(
            self::QUEUE_DELAYED,
            $since,
            '+inf',
            ['withscores' => true]
        );

        if (!empty($resultRaw)) {
            $keys = [];
            foreach ($resultRaw as $jobId => $timestamp) {
                if (empty($eventType) || strpos($jobId, "$eventType:") === 0) {
                    $keys[$jobId] = $timestamp;
                }
            }

            if (!empty($keys)) {
                $events = $redis->hmget(self::META_DATA, array_keys($keys));
                foreach ($events as $jobId => $rawJob) {
                    /** @var Job $job */
                    $job = unserialize(base64_decode($rawJob));
                    yield $job->jobId => $job;
                }
            }
        }

        $immediate = $redis->lrange(self::QUEUE_IMMEDIATE, 0, 100);
        foreach ($immediate as $rawJob) {
            list($jobId, $rawJob) = explode('#', $rawJob, 2);
            if (empty($eventType) || strpos($jobId, "$eventType:") === 0) {
                /** @var Job $job */
                $job = unserialize(base64_decode($rawJob));
                yield $job->jobId => $job;
            }
        }
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
     * @return integer
     */
    public function countAllJobs()
    {
        $delayed   = $this->getRedis()->ZCARD(self::QUEUE_DELAYED);
        $immediate = $this->getRedis()->LLEN(self::QUEUE_IMMEDIATE);

        return $delayed + $immediate;
    }
}
