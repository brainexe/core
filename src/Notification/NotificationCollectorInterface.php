<?php

namespace Matze\Core\Notification;

interface NotificationCollectorInterface {

    /**
     * @return array (key: identifier, value: amount)
     */
    public function getNotification();
} 