<?php

namespace BrainExe\Core\Cron;

interface CronDefinition
{
    /**
     * @return string[]
     */
    public static function getCrons();
}
