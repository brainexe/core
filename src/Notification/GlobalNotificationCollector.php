<?php

namespace BrainExe\Core\Notification;

/**
 * @Service(public=false)
 */
class GlobalNotificationCollector implements NotificationCollectorInterface
{

    /**
     * @var NotificationCollectorInterface[]
     */
    private $collectors = [];

    /**
     * @param NotificationCollectorInterface $collector
     */
    public function addCollector(NotificationCollectorInterface $collector)
    {
        $this->collectors[] = $collector;
    }

    /**
     * @{@inheritdoc}
     */
    public function getNotification()
    {
        $notifications = [];
        foreach ($this->collectors as $collector) {
            $notifications = array_merge($notifications, $collector->getNotification());
        }

        return $notifications;
    }
}
