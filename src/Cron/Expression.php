<?php

namespace BrainExe\Core\Cron;

use BrainExe\Core\Annotations\Service;
use Cron\CronExpression;

/**
 * @Service
 */
class Expression
{

    /**
     * @param string $expression
     * @param string $currentTime
     * @return int
     */
    public function getNextRun(string $expression, $currentTime = 'now') : int
    {
        $cron = CronExpression::factory($expression);

        return $cron->getNextRunDate($currentTime)->getTimestamp();
    }
}
