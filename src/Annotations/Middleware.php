<?php

namespace BrainExe\Core\Annotations;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Annotations\Builder\MiddlewareDefinitionBuilder;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;

/**
 * @Annotation
 */
class Middleware extends Service
{

    /**
     * @var int
     */
    public $priority = 5;

    /**
     * {@inheritdoc}
     */
    public static function getBuilder(Reader $reader)
    {
        return new MiddlewareDefinitionBuilder($reader);
    }
}
