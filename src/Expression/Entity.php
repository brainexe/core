<?php

namespace BrainExe\Core\Expression;

class Entity
{

    /**
     * @var string
     */
    public $expressionId;

    /**
     * @var string
     */
    public $condition;

    /**
     * @var string[]
     */
    public $actions;

    /**
     * @var int
     */
    public $counter;

    /**
     * @var mixed
     */
    public $payload;

}
