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
     * @param string $event
     * @param int|null $priority
     */
    public function __construct($event, $priority = null)
    {
        if (is_array($event)) {
            $this->event = $event['value'];
        } else {
            $this->event = $event;
        }
        $this->priority = $priority;
    }
}
