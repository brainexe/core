<?php

namespace Tests\BrainExe\Core\MessageQueue;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\BackgroundEvent;
use BrainExe\Core\EventDispatcher\DelayedEvent;
use BrainExe\Core\EventDispatcher\CronEvent;
use BrainExe\Core\MessageQueue\Listener;
use BrainExe\Core\MessageQueue\Gateway;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class ListenerTest extends TestCase
{

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    /**
     * @var Listener
     */
    private $subject;

    public function setup()
    {
        $this->gateway = $this->createMock(Gateway::class);

        $this->subject = new Listener($this->gateway);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            DelayedEvent::DELAYED => 'onDelayedEvent',
            BackgroundEvent::BACKGROUND => 'onBackgroundEvent'
        ];
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testOnDelayedEvent()
    {
        /** @var AbstractEvent $event */
        $event          = $this->createMock(AbstractEvent::class);
        $eventTimestamp = 10000;
        $delayedEvent   = new DelayedEvent($event, $eventTimestamp);

        $this->gateway
            ->expects($this->once())
            ->method('addEvent')
            ->with($event, $eventTimestamp);

        $this->subject->onDelayedEvent($delayedEvent);
    }

    public function testOnBackgroundEvent()
    {
        /** @var AbstractEvent $event */
        $event          = $this->createMock(AbstractEvent::class);
        $delayedEvent   = new BackgroundEvent($event);

        $this->gateway
            ->expects($this->once())
            ->method('addEvent')
            ->with($event, 0);

        $this->subject->onBackgroundEvent($delayedEvent);
    }

    public function testOnIntervalEvent()
    {
        /** @var AbstractEvent $event */
        $event          = $this->createMock(AbstractEvent::class);
        $intervalEvent  = new CronEvent($event, '@daily');

        $this->gateway
            ->expects($this->once())
            ->method('addEvent')
            ->with($intervalEvent, $this->isType('int'));

        $this->subject->onCronEvent($intervalEvent);
    }
}
