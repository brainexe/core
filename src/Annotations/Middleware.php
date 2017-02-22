<?php

namespace BrainExe\Core\Annotations;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Annotations\Builder\Middleware as Builder;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Annotation
 * @api
 */
class Middleware extends Service
{

    /**
     * {@inheritdoc}
     */
    public static function getBuilder(ContainerBuilder $container, Reader $reader)
    {
        return new Builder($container, $reader);
    }
}
