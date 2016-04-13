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
 * @Covers BrainExe\Core\MessageQueue\Gateway
 */
class GatewayTest extends TestCase
{
    const DUMMY_EVENT  = 'TzozMDoiQnJhaW5FeGVcQ29yZVxNZXNzYWdlUXVldWVcSm9iIjo0OntzOjU6ImV2ZW50IjtPOjM5OiJCcmFpbkV4ZVxDb3JlXEV2ZW50RGlzcGF0Y2hlclxDcm9uRXZlbnQiOjQ6e3M6MTA6ImV4cHJlc3Npb24iO3M6NjoiQGRhaWx5IjtzOjU6ImV2ZW50IjtPOjQ4OiJCcmFpbkV4ZVxDb3JlXEV2ZW50RGlzcGF0Y2hlclxFdmVudHNcVGltaW5nRXZlbnQiOjM6e3M6ODoidGltaW5nSWQiO3M6NToiZGFpbHkiO3M6OToiZXZlbnROYW1lIjtzOjY6InRpbWluZyI7czo1OToiAFN5bWZvbnlcQ29tcG9uZW50XEV2ZW50RGlzcGF0Y2hlclxFdmVudABwcm9wYWdhdGlvblN0b3BwZWQiO2I6MDt9czo5OiJldmVudE5hbWUiO3M6MTg6Im1lc3NhZ2VfcXVldWUuY3JvbiI7czo1OToiAFN5bWZvbnlcQ29tcG9uZW50XEV2ZW50RGlzcGF0Y2hlclxFdmVudABwcm9wYWdhdGlvblN0b3BwZWQiO2I6MDt9czo1OiJqb2JJZCI7czoyMjoibWVzc2FnZV9xdWV1ZS5jcm9uOjMxMCI7czo5OiJ0aW1lc3RhbXAiO2k6MTQ0ODMxOTYwMDtzOjEyOiJlcnJvckNvdW50ZXIiO2k6MDt9';
    const DUMMY_EVENT2 = 'TzozMDoiQnJhaW5FeGVcQ29yZVxNZXNzYWdlUXVldWVcSm9iIjo0OntzOjU6ImV2ZW50IjtPOjM5OiJCcmFpbkV4ZVxDb3JlXEV2ZW50RGlzcGF0Y2hlclxDcm9uRXZlbnQiOjQ6e3M6MTA6ImV4cHJlc3Npb24iO3M6OToiKiAqICogKiAqIjtzOjU6ImV2ZW50IjtPOjQ4OiJCcmFpbkV4ZVxDb3JlXEV2ZW50RGlzcGF0Y2hlclxFdmVudHNcVGltaW5nRXZlbnQiOjM6e3M6ODoidGltaW5nSWQiO3M6NjoibWludXRlIjtzOjEwOiJldmVudF9uYW1lIjtzOjY6InRpbWluZyI7czo1OToiAFN5bWZvbnlcQ29tcG9uZW50XEV2ZW50RGlzcGF0Y2hlclxFdmVudABwcm9wYWdhdGlvblN0b3BwZWQiO2I6MDt9czoxMDoiZXZlbnRfbmFtZSI7czoxODoibWVzc2FnZV9xdWV1ZS5jcm9uIjtzOjU5OiIAU3ltZm9ueVxDb21wb25lbnRcRXZlbnREaXNwYXRjaGVyXEV2ZW50AHByb3BhZ2F0aW9uU3RvcHBlZCI7YjowO31zOjU6ImpvYklkIjtzOjIyOiJtZXNzYWdlX3F1ZXVlLmNyb246MzA1IjtzOjk6InRpbWVzdGFtcCI7aToxNDQ4MjI3MzgwO3M6MTI6ImVycm9yQ291bnRlciI7aTowO30';
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
        $this->time         = $this->getMock(Time::class, [], [], '', false);
        $this->redis        = $this->getRedisMock();
        $this->idGenerator  = $this->getMock(IdGenerator::class, [], [], '', false);

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
                'event:11#' . self::DUMMY_EVENT,
                'event:10#' . self::DUMMY_EVENT2,
            ]);

        $this->redis
            ->expects($this->once())
            ->method('lrem')
            ->with(Gateway::QUEUE_IMMEDIATE, 1, 'event:10#' . self::DUMMY_EVENT2)
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
        /** @var MockObject|AbstractEvent $event */
        $event     = $this->getMock(AbstractEvent::class, [], ['type']);
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
        /** @var MockObject|AbstractEvent $event */
        $event     = $this->getMock(AbstractEvent::class, [], ['type']);
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
                $timestamp,
                "type:$eventId"
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
            ->willReturn([$eventId => self::DUMMY_EVENT]);

        $this->redis
            ->expects($this->once())
            ->method('lrange')
            ->with(Gateway::QUEUE_IMMEDIATE)
            ->willReturn(['espeak.speak:86#' . self::DUMMY_EVENT2]);

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

        $eventRaw = self::DUMMY_EVENT;

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
            ->with(Gateway::QUEUE_DELAYED, 1000 + Gateway::RETRY_TIME, 'event:100');

        $event = new TestEvent('test');
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
}
