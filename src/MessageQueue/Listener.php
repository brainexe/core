<?php

namespace BrainExe\Core\MessageQueue;

use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\EventDispatcher\BackgroundEvent;
use BrainExe\Core\EventDispatcher\DelayedEvent;
use BrainExe\Core\EventDispatcher\CronEvent;
use Cron\CronExpression;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener(name="MessageQueue.Listener")
 */
class Listener implements EventSubscriberInterface
{

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            DelayedEvent::DELAYED       => 'onDelayedEvent',
            CronEvent::CRON             => 'onCronEvent',
            BackgroundEvent::BACKGROUND => 'onBackgroundEvent'
        ];
    }

    /**
     * @param DelayedEvent $event
     */
    public function onDelayedEvent(DelayedEvent $event)
    {
        $job = $this->gateway->addEvent($event->getEvent(), $event->getTimestamp());
        $event->setJob($job);
    }

    /**
     * @param CronEvent $event
     */
    public function onCronEvent(CronEvent $event)
    {
        $cron = CronExpression::factory($event->getExpression());

        $job = $this->gateway->addEvent($event, $cron->getNextRunDate()->getTimestamp());
        $event->setJob($job);
    }

    /**
     * @param BackgroundEvent $event
     */
    public function onBackgroundEvent(BackgroundEvent $event)
    {
        $job = $this->gateway->addEvent($event->getEvent(), 0);
        $event->setJob($job);
    }
}
