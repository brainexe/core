<?php

namespace BrainExe\Core\Annotations;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Annotations\Builder\Controller as Builder;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Annotation
 * @api
 */
class Controller extends Service
{
    /**
     * Global Requirements which are applied to all matching @Routes
     * @var array
     */
    public $requirements = array();

    /**
     * {@inheritdoc}
     */
    public static function getBuilder(ContainerBuilder $container, Reader $reader)
    {
        return new Builder($container, $reader);
    }
}
