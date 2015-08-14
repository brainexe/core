<?php

namespace BrainExe\Core\Redis;

use BrainExe\Annotations\Annotations\Service;
use Predis\Client;

/**
 * @api
 * @Service("redis", public=false)
 */
class Predis extends Client
{

    /**
     * @param string $name
     * @param string $command
     */
    public function defineCommand($name, $command)
    {
        $this->getProfile()->defineCommand($name, $command);
    }
}
