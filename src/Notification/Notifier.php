<?php

namespace BrainExe\Core\Notification;

use BrainExe\Annotations\Annotations\Service;
use Monolog\Logger;

/**
 * @Service("Core.Notification.Notifier", public=false)
 */
class Notifier extends Logger
{

}
