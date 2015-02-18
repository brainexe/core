<?php

namespace BrainExe\Core\Util;

use BrainExe\Annotations\Annotations\Service;

/**
 * @Service(public=false)
 */
class Time
{

    /**
     * @return integer
     */
    public function now()
    {
        return time();
    }

    /**
     * @param string $format
     * @return string
     */
    public function date($format)
    {
        return date($format);
    }

    /**
     * @return integer
     */
    public function microtime()
    {
        return microtime(true);
    }

    /**
     * @param string $string
     * @return integer
     */
    public function strtotime($string)
    {
        return strtotime($string);
    }
}
