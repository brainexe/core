<?php

namespace BrainExe\Core\Stats;

use BrainExe\Core\EventDispatcher\AbstractEvent;

/**
 * @api
 */
class Event extends AbstractEvent
{

    const INCREASE = 'stats.increase';
    const SET      = 'stats.set';

    /**
     * @var string
     */
    private $key;

    /**
     * @var int
     */
    private $value;

    /**
     * @param string $eventName self::*
     * @param string $key
     * @param int $value
     */
    public function __construct(string $eventName, string $key, int $value = 1)
    {
        parent::__construct($eventName);

        $this->key   = $key;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue() : int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }
}
