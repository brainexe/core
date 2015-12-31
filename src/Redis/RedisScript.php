<?php

namespace BrainExe\Core\Redis;

use Predis\Command\ScriptCommand;

/**
 * @api
 */
abstract class RedisScript extends ScriptCommand
{

    /**
     * @return string
     */
    abstract public function getName();
}
