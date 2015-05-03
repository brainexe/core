<?php

namespace BrainExe\Core\Stats;

use BrainExe\Core\EventDispatcher\AbstractEvent;

/**
 * @api
 */
class Event extends AbstractEvent
{

    const INCREASE = 'stats:increase';
    const SET      = 'stats:set';

    /**
     * @var string
     */
    private $key;

    /**
     * @var int
     */
    private $value;


    public function __construct($eventName, $key, $value)
    {
        parent::__construct($eventName);

        $this->key   = $key;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
