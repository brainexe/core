<?php

namespace BrainExe\Core\Cron;

use BrainExe\Core\Annotations\Service;

/**
 * @Service("Crons.Default", public=false, tags={{"name"="cron"}})
 */
class DefaultCrons implements CronDefinition
{
    const MINUTE = 'minute';
    const HOURLY = 'hourly';
    const DAILY  = 'daily';

    /**
     * @return string[]
     */
    public static function getCrons()
    {
        return [
            self::MINUTE => '* * * * *',
            self::HOURLY => '@hourly',
            self::DAILY  => '@daily',
        ];
    }
}
