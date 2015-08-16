<?php

namespace BrainExe\Core\Notification;

use BrainExe\Core\Annotations\EventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("Notification.Listener")
 */
class Listener implements EventSubscriberInterface
{
    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @param Notifier $notifier
     */
    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Notification::NOTIFY => 'notify'
        ];
    }

    /**
     * @param Notification $event
     */
    public function notify(Notification $event)
    {
        $this->notifier->addRecord($event->getLevel(), $event->getMessage(), [
        ]);
    }
}
