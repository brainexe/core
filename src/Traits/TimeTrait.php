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
     * @Inject("@Time")
     * @param Time $time
     */
    public function setTime(Time $time)
    {
        $this->time = $time;
    }

    /**
     * @todo private/protected
     * @return Time
     */
    public function getTime() : Time
    {
        return $this->time;
    }

    /**
     * @todo private/protected
     * @return int
     */
    public function now() : int
    {
        return $this->time->now();
    }
}
