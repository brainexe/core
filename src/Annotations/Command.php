<?php

namespace BrainExe\Core\Annotations;

use BrainExe\Core\Annotations\Builder\Command as Builder;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Annotation
 * @api
 */
class Command extends Service
{
    /**
     * {@inheritdoc}
     */
    public static function getBuilder(ContainerBuilder $container, Reader $reader)
    {
        return new Builder($container, $reader);
    }
}
