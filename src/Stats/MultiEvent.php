<?php

namespace BrainExe\Core\Stats;

use BrainExe\Core\EventDispatcher\AbstractEvent;

/**
 * @api
 */
class MultiEvent extends AbstractEvent
{

    const INCREASE = 'stats.multi.increase';
    const SET      = 'stats.multi.set';

    /**
     * @var array
     */
    private $values;

    /**
     * @param string $eventName self::*
     * @param array $values
     */
    public function __construct(string $eventName, array $values)
    {
        parent::__construct($eventName);

        $this->values = $values;
    }

    /**
     * @return array
     */
    public function getValues() : array
    {
        return $this->values;
    }
}
