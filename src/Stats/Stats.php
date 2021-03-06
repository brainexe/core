<?php

namespace BrainExe\Core\Stats;

use BrainExe\Core\Annotations\Service;

/**
 * @Service
 */
class Stats
{

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param array $values
     */
    public function increase(array $values)
    {
        $this->gateway->increase($values);
    }

    /**
     * @param array $values
     */
    public function set(array $values)
    {
        foreach ($values as $key => $value) {
            $this->gateway->set($key, $value);
        }
    }

    /**
     * @return int[]
     */
    public function getAll() : array
    {
        return $this->gateway->getAll();
    }

    /**
     * @param string $key
     * @return int
     */
    public function get(string $key) : int
    {
        return $this->gateway->get($key);
    }
}
