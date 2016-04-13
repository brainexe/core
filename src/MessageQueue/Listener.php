<?php

namespace BrainExe\Core\MessageQueue;

use BrainExe\Annotations\Annotations\Inject;
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
     * @Inject("@MessageQueue.Gateway")
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
        $this->gateway->addEvent($event->getEvent(), $event->getTimestamp());
    }

    /**
     * @param CronEvent $event
     */
    public function onCronEvent(CronEvent $event)
    {
        $cron = CronExpression::factory($event->getExpression());

        $this->gateway->addEvent($event, $cron->getNextRunDate()->getTimestamp());
    }

    /**
     * @param BackgroundEvent $event
     */
    public function onBackgroundEvent(BackgroundEvent $event)
    {
        $this->gateway->addEvent($event->getEvent(), 0);
    }
}
