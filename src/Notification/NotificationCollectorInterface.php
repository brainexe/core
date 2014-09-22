<?php

namespace BrainExe\Core\Notification;

interface NotificationCollectorInterface {

    /**
     * @return array (key: identifier, value: amount)
     */
    public function getNotification();
} 