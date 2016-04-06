<?php

namespace BrainExe\Core\Annotations;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Annotations\Builder\CompilerPass as Builder;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;

/**
 * @Annotation
 * @api
 */
class CompilerPass extends Service
{

    /**
     * @var integer
     */
    public $priority = 3;

    /**
     * {@inheritdoc}
     */
    public static function getBuilder(Reader $reader)
    {
        return new Builder($reader);
    }
}
