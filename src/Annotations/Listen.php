<?php

namespace BrainExe\Core\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @api
 */
class Listen
{

    /**
     * @var string
     */
    public $event;

    /**
     * @var string
     */
    public $priority;

    /**
     * @param string|array $event
     * @param int|null $priority
     */
    public function __construct($event, $priority = null)
    {
        $this->event    = is_array($event) ? $event['value'] : $event;
        $this->priority = $priority;
    }
}
