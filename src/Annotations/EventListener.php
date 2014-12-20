<?php

namespace BrainExe\Core\Annotations;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Annotations\Builder\EventListenerDefinitionBuilder;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;

/**
 * @Annotation
 */
class EventListener extends Service
{

    /**
     * {@inheritdoc}
     */
    public static function getBuilder(Reader $reader)
    {
        return new EventListenerDefinitionBuilder($reader);
    }
}
