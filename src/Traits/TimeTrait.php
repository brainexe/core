<?php

namespace BrainExe\Core\Traits;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Util\Time;

/**
 * @api
 */
trait TimeTrait
{

    /**
     * @var Time
     */
    private $time;

    /**
     * @param Time $time
     */
    public function setTime(Time $time)
    {
        $this->time = $time;
    }

    /**
     * @return Time
     */
    public function getTime() : Time
    {
        return $this->time;
    }

    /**
     * @return int
     */
    public function now() : int
    {
        return $this->time->now();
    }
}
