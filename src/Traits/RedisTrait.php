<?php

namespace BrainExe\Core\Traits;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Redis\Predis;
use Predis\Client;

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
     * @Inject("@Redis")
     * @param Predis $client
     */
    public function setRedis(Predis $client)
    {
        $this->redis = $client;
    }

    /**
     * @return Predis
     */
    protected function getRedis()
    {
        return $this->redis;
    }
}
