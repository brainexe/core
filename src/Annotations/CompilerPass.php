<?php

namespace BrainExe\Core\Annotations;

use BrainExe\Core\Annotations\Builder\CompilerPass as Builder;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Annotation
 * @api
 */
class CompilerPass extends Service
{

    /**
     * @var int
     */
    public $priority = 3;

    /**
     * @see PassConfig::TYPE_*
     * @var string|null
     */
    public $type = PassConfig::TYPE_BEFORE_OPTIMIZATION;

    /**
     * {@inheritdoc}
     */
    public static function getBuilder(ContainerBuilder $container, Reader $reader)
    {
        return new Builder($container, $reader);
    }
}
