<?php

namespace BrainExe\Core\Redis;

use Predis\Client;
use Predis\Pipeline\Pipeline;

class Predis extends Client implements RedisInterface
{
    /**
     * @return Pipeline
     */
    public function multi() {
        return $this->pipeline();
    }

    public function exec() {
        return $this->execute();
    }
}
