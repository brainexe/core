<?php

namespace BrainExe\Core\Annotations;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Annotations\Builder\Controller as Builder;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;

/**
 * @Annotation
 * @api
 */
class Controller extends Service
{

    /**
     * {@inheritdoc}
     */
    public static function getBuilder(Reader $reader)
    {
        return new Builder($reader);
    }
}
