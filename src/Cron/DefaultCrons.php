<?php

namespace BrainExe\Core;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Cron\CronDefinition;

/**
 * @Service("Crons.Default", public=false, tags={{"name"="cron"}})
 */
class DefaultCrons implements CronDefinition
{

    /**
     * @return string[]
     */
    public static function getCrons()
    {
        return [
            'daily'  => '@daily',
            'hourly' => '@hourly'
        ];
    }
}
