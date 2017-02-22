<?php

namespace BrainExe\Core\Util;

use BrainExe\Core\Annotations\Service;

/**
 * @Service
 * @api
 */
class Time
{

    /**
     * @return int
     */
    public function now() : int
    {
        return time();
    }

    /**
     * @param string $format
     * @return string
     */
    public function date($format) : string
    {
        return date($format);
    }

    /**
     * @return int
     */
    public function microtime() : int
    {
        return microtime(true);
    }

    /**
     * @param string $string
     * @return int
     */
    public function strtotime(string $string)
    {
        return strtotime($string);
    }
}
