<?php

namespace BrainExe\Core\Annotations;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Annotations\Builder\Middleware as Builder;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;

/**
 * @Annotation
 * @api
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
        return new Builder($reader);
    }
}
