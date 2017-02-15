<?php

namespace Tests\BrainExe\Core\MessageQueue;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\CronEvent;
use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Core\Util\Time;
use BrainExe\Core\MessageQueue\Gateway;
use BrainExe\Core\MessageQueue\Job;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class TestEvent extends AbstractEvent
{
}

/**
 * @covers \BrainExe\Core\MessageQueue\Gateway
 */
class GatewayTest extends TestCase
{
    const DUMMY_EVENT  = 'O:30:"BrainExe\\Core\\MessageQueue\\Job":4:{s:5:"event";O:39:"BrainExe\\Core\\EventDispatcher\\CronEvent":4:{s:10:"expression";s:6:"@daily";s:5:"event";O:48:"BrainExe\\Core\\EventDispatcher\\Events\\TimingEvent":3:{s:8:"timingId";s:5:"daily";s:9:"eventName";s:13:"timing.timing";s:59:"' . "\0" . 'Symfony\\Component\\EventDispatcher\\Event' . "\0" . 'propagationStopped";b:0;}s:9:"eventName";s:18:"message_queue.cron";s:59:"' . "\0" . 'Symfony\\Component\\EventDispatcher\\Event' . "\0" . 'propagationStopped";b:0;}s:5:"jobId";s:22:"message_queue.cron:310";s:9:"timestamp";i:1448319600;s:12:"errorCounter";i:0;}';
    const DUMMY_EVENT2 = 'O:30:"BrainExe\\Core\\MessageQueue\\Job":4:{s:5:"event";O:39:"BrainExe\\Core\\EventDispatcher\\CronEvent":4:{s:10:"expression";s:9:"* * * * *";s:5:"event";O:48:"BrainExe\\Core\\EventDispatcher\\Events\\TimingEvent":3:{s:8:"timingId";s:6:"minute";s:10:"event_name";s:13:"timing.timing";s:59:"' . "\0" . 'Symfony\\Component\\EventDispatcher\\Event' . "\0" . 'propagationStopped";b:0;}s:10:"event_name";s:18:"message_queue.cron";s:59:"' . "\0" . 'Symfony\\Component\\EventDispatcher\\Event' . "\0" . 'propagationStopped";b:0;}s:5:"jobId";s:22:"message_queue.cron:305";s:9:"timestamp";i:1448227380;s:12:"errorCounter";i:0;}'.'O:30:"BrainExe\\Core\\MessageQueue\\Job":4:{s:5:"event";O:39:"BrainExe\\Core\\EventDispatcher\\CronEvent":4:{s:10:"expression";s:6:"@daily";s:5:"event";O:48:"BrainExe\\Core\\EventDispatcher\\Events\\TimingEvent":3:{s:8:"timingId";s:5:"daily";s:9:"eventName";s:6:"timing";s:59:"' . "\0" . 'Symfony\\Component\\EventDispatcher\\Event' . "\0" . 'propagationStopped";b:0;}s:9:"eventName";s:18:"message_queue.cron";s:59:"' . "\0" . 'Symfony\\Component\\EventDispatcher\\Event' . "\0" . 'propagationStopped";b:0;}s:5:"jobId";s:22:"message_queue.cron:310";s:9:"timestamp";i:1448319600;s:12:"errorCounter";i:0;}';

    use RedisMockTrait;

    /**
     * @var Gateway
     */
    private $subject;

    /**
     * @var Time|MockObject
     */
    private $time;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->time         = $this->createMock(Time::class);
        $this->redis        = $this->getRedisMock();
        $this->idGenerator  = $this->createMock(IdGenerator::class);

        $this->subject = new Gateway();
        $this->subject->setTime($this->time);
        $this->subject->setRedis($this->redis);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testDeleteDelayedEventWithEventType()
    {
        $eventId   = 10;
        $eventType = 'event';

        $this->redis
            ->expects($this->once())
            ->method('zrem')
            ->with(Gateway::QUEUE_DELAYED, "event:10")
            ->willReturn(1);

        $this->redis
            ->expects($this->once())
            ->method('hdel')
            ->with(Gateway::META_DATA, ["event:10"]);

        $actual = $this->subject->deleteEvent($eventId, $eventType);
        $this->assertTrue($actual);
    }

    public function testDeleteImmediateEventWithEventType()
    {
        $eventId   = 10;
        $eventType = 'event';

        $this->redis
            ->expects($this->once())
            ->method('zrem')
            ->with(Gateway::QUEUE_DELAYED, "event:10")
            ->willReturn(0);

        $this->redis
            ->expects($this->once())
            ->method('lrange')
            ->with(Gateway::QUEUE_IMMEDIATE)
            ->willReturn([
                'event:11#' . base64_encode(self::DUMMY_EVENT),
                'event:10#' . base64_encode(self::DUMMY_EVENT2),
            ]);

        $this->redis
            ->expects($this->once())
            ->method('lrem')
            ->with(Gateway::QUEUE_IMMEDIATE, 1, 'event:10#' . base64_encode(self::DUMMY_EVENT2))
            ->willReturn(1);

        $actual = $this->subject->deleteEvent($eventId, $eventType);
        $this->assertTrue($actual);
    }

    public function testDeleteNotFound()
    {
        $eventId   = 10;
        $eventType = 'event';

        $this->redis
            ->expects($this->once())
            ->method('zrem')
            ->with(Gateway::QUEUE_DELAYED, "event:10")
            ->willReturn(0);

        $this->redis
            ->expects($this->once())
            ->method('lrange')
            ->with(Gateway::QUEUE_IMMEDIATE)
            ->willReturn([]);

        $actual = $this->subject->deleteEvent($eventId, $eventType);
        $this->assertFalse($actual);
    }

    public function testAddEvent()
    {
        $event     = $this->getTestEvent('type');
        $timestamp = 0;
        $eventId   = 100;

        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->willReturn($eventId);

        $this->redis
            ->expects($this->at(0))
            ->method('pipeline')
            ->willReturnSelf();
        $this->redis
            ->expects($this->at(1))
            ->method('lpush')
            ->with(Gateway::QUEUE_IMMEDIATE);
        $this->redis
            ->expects($this->at(2))
            ->method('exec');

        $this->subject->addEvent($event, $timestamp);
    }

    public function testAddEventDelayed()
    {
        $event = $this->getTestEvent('type');

        $timestamp = 120000;
        $eventId   = 100;

        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->willReturn($eventId);

        $this->redis
            ->expects($this->at(0))
            ->method('pipeline')
            ->willReturnSelf();
        $this->redis
            ->expects($this->at(1))
            ->method('hset')
            ->with(
                Gateway::META_DATA,
                "type:$eventId"
            );
        $this->redis
            ->expects($this->at(2))
            ->method('zadd')
            ->with(
                Gateway::QUEUE_DELAYED,
                ["type:$eventId" => $timestamp]
            );
        $this->redis
            ->expects($this->at(3))
            ->method('exec');

        $this->subject->addEvent($event, $timestamp);
    }

    public function testGetEventsByTypeWithoutData()
    {
        $eventType   = 'type';
        $since       = 999;

        $raw = [];

        $this->redis
            ->expects($this->once())
            ->method('zrangebyscore')
            ->with(
                Gateway::QUEUE_DELAYED,
                $since,
                '+inf',
                ['withscores' => true]
            )
            ->willReturn($raw);

        $this->redis
            ->expects($this->once())
            ->method('lrange')
            ->with(Gateway::QUEUE_IMMEDIATE)
            ->willReturn([]);

        $actual = $this->subject->getEventsByType($eventType, $since);

        $expected = [];
        $this->assertEquals($expected, $actual);
    }

    public function testGetEventsByTypeWithData()
    {
        $eventType = 'espeak.speak';
        $since     = 999;

        $raw = [
            $eventId = 'espeak.speak:id' => $timestamp = 800
        ];

        $this->redis
            ->expects($this->once())
            ->method('zrangebyscore')
            ->with(
                Gateway::QUEUE_DELAYED,
                $since,
                '+inf',
                ['withscores' => true]
            )
            ->willReturn($raw);

        $this->redis
            ->expects($this->once())
            ->method('hmget')
            ->with(Gateway::META_DATA, [$eventId])
            ->willReturn([$eventId => base64_encode(self::DUMMY_EVENT)]);

        $this->redis
            ->expects($this->once())
            ->method('lrange')
            ->with(Gateway::QUEUE_IMMEDIATE)
            ->willReturn(['espeak.speak:86#' . base64_encode(self::DUMMY_EVENT2)]);

        $actual = $this->subject->getEventsByType($eventType, $since);
        $this->assertEquals(['message_queue.cron:310', 'message_queue.cron:305'], array_keys($actual));
    }

    public function testGetEventsByTypeWithWildcard()
    {
        $since = 999;

        $raw = [
            $eventId = 'type:id' => $timestamp = 800
        ];

        $event = new CronEvent(new TimingEvent('daily'), '@daily');

        $eventRaw = base64_encode(self::DUMMY_EVENT);

        $this->redis
            ->expects($this->once())
            ->method('zrangebyscore')
            ->with(
                Gateway::QUEUE_DELAYED,
                $since,
                '+inf',
                ['withscores' => true]
            )
            ->willReturn($raw);

        $this->redis
            ->expects($this->once())
            ->method('hmget')
            ->with(Gateway::META_DATA, [$eventId])
            ->willReturn([$eventId => $eventRaw]);

        $this->redis
            ->expects($this->once())
            ->method('lrange')
            ->with(Gateway::QUEUE_IMMEDIATE)
            ->willReturn([]);

        $actual = $this->subject->getEventsByType(null, $since);

        $expected = [
            'message_queue.cron:310' => new Job($event, 'message_queue.cron:310', 1448319600)
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testRestoreJob()
    {
        $now = 1000;
        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturnSelf();

        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with(Gateway::META_DATA, 'event:100');

        $this->redis
            ->expects($this->once())
            ->method('zadd')
            ->with(Gateway::QUEUE_DELAYED, [
                'event:100' => 1000 + Gateway::RETRY_TIME
            ]);

        $event = $this->getTestEvent('test');

        $job = new Job($event, 'event:100', 100);

        $this->subject->restoreJob($job);
    }

    public function testCountJobs()
    {
        $this->redis
            ->expects($this->once())
            ->method('zcard')
            ->with(Gateway::QUEUE_DELAYED)
            ->will($this->returnValue(12));

        $this->redis
            ->expects($this->once())
            ->method('llen')
            ->with(Gateway::QUEUE_IMMEDIATE)
            ->will($this->returnValue(3));

        $actual = $this->subject->countAllJobs();

        $this->assertEquals(15, $actual);
    }

    /**
     * @param string $type
     * @return AbstractEvent
     */
    private function getTestEvent(string $type)
    {
        return new TestEvent($type);
    }
}
