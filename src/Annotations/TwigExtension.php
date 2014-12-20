<?php

namespace BrainExe\Core\Annotations;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Annotations\Builder\TwigExtensionDefinitionBuilder;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;

/**
 * @Annotation
 */
class TwigExtension extends Service
{

    /**
     * @var boolean
     */
    public $compiler = false;

    /**
     * {@inheritdoc}
     */
    public static function getBuilder(Reader $reader)
    {
        return new TwigExtensionDefinitionBuilder($reader);
    }
}
