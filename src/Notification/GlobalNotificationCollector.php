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
    private $_collectors = [];

    /**
     * @param NotificationCollectorInterface $collector
     */
    public function addCollector(NotificationCollectorInterface $collector)
    {
        $this->_collectors[] = $collector;
    }

    /**
     * @{@inheritdoc}
     */
    public function getNotification()
    {
        $notifications = [];
        foreach ($this->_collectors as $collector) {
            $notifications = array_merge($notifications, $collector->getNotification());
        }

        return $notifications;
    }
}
