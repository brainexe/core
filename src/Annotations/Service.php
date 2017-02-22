<?php

namespace BrainExe\Core\Annotations;

use BrainExe\Core\Annotations\Builder\ServiceDefinition;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Annotation
 */
class Service extends Annotation
{

    /**
     * @param ContainerBuilder $container
     * @param Reader $reader
     * @return ServiceDefinition
     */
    public static function getBuilder(ContainerBuilder $container, Reader $reader)
    {
        return new ServiceDefinition($container, $reader);
    }

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $public = false;

    /**
     * @var mixed
     */
    public $configurator;

    /**
     * @var string|array
     */
    public $factory;

    /**
     * @var array[]
     */
    public $tags = [];

    /**
     * @var bool
     */
    public $lazy = false;

    /**
     * @var bool
     */
    public $shared = true;

    /**
     * @var bool
     */
    public $synthetic = false;

    /**
     * @var bool
     */
    public $abstract = false;
}
