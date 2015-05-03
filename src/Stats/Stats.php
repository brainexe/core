<?php

namespace BrainExe\Core\Stats;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

/**
 * @Service("Stats.Stats", public=false)
 */
class Stats
{

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject("@Stats.Gateway")
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param string $key
     * @param int $value
     */
    public function increase($key, $value = 1)
    {
        $this->gateway->increase($key, $value);
    }

    /**
     * @param string $key
     * @param int $value
     */
    public function set($key, $value)
    {
        $this->gateway->set($key, $value);
    }

    /**
     * @return int[]
     */
    public function getAll()
    {
        return $this->gateway->getAll();
    }

    /**
     * @param string $key
     * @return int
     */
    public function get($key)
    {
        return $this->gateway->get($key);
    }
}
