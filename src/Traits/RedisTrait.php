<?php

namespace BrainExe\Core\Traits;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Redis\Predis;

/**
 * @api
 */
trait RedisTrait
{

    /**
     * @var Predis
     */
    private $redis;

    /**
     * @Inject
     * @param Predis $client
     */
    public function setRedis(Predis $client)
    {
        $this->redis = $client;
    }

    /**
     * @return Predis
     */
    protected function getRedis() : Predis
    {
        return $this->redis;
    }
}
