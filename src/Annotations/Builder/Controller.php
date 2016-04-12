<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Builder\ServiceDefinition;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Role;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\DependencyInjection\CompilerPass\ControllerCompilerPass;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Definition;

class Controller extends ServiceDefinition
{

    /**
     * @todo private!
     * @var string
     */
    protected $serviceId;

    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build(
            $reflectionClass,
            $annotation
        );
        $this->serviceId = $serviceId = sprintf('__controller.%s', $serviceId);

        $definition->addTag(ControllerCompilerPass::CONTROLLER_TAG);
        $definition->setPublic(true);
        $definition->setShared(false);

        foreach ($reflectionClass->getMethods() as $method) {
            /** @var Route $routeAnnotation */
            $routeAnnotation = $this->reader->getMethodAnnotation($method, Route::class);

            if ($routeAnnotation) {
                /** @var Guest $guestAnnotation */
                $guestAnnotation = $this->reader->getMethodAnnotation($method, Guest::class);

                /** @var Role $roleAnnotation */
                $roleAnnotation = $this->reader->getMethodAnnotation($method, Role::class);

                $this->setDefaults(
                    $routeAnnotation,
                    $method,
                    $annotation,
                    $guestAnnotation,
                    $roleAnnotation
                );

                $definition->addTag(ControllerCompilerPass::ROUTE_TAG, [$routeAnnotation]);
            }
        }

        return [$serviceId, $definition];
    }


    /**
     * @param Route $routeAnnotation
     * @param ReflectionMethod $method
     * @param ControllerAnnotation $controller
     * @param Guest $guestAnnotation
     * @param Role $roleAnnotation
     */
    protected function setDefaults(
        Route $routeAnnotation,
        ReflectionMethod $method,
        ControllerAnnotation $controller,
        Guest $guestAnnotation = null,
        Role $roleAnnotation = null
    ) {
        $defaults = $routeAnnotation->getDefaults();
        $defaults['_controller'] = [
            $this->serviceId,
            $method->getName()
        ];
        if ($guestAnnotation) {
            $defaults['_guest'] = true;
        }

        if ($roleAnnotation) {
            $defaults['_role'] = $roleAnnotation->role;
        }


        if ($controller->requirements) {
            $routeAnnotation->setRequirements(
                array_merge($routeAnnotation->getRequirements(), $controller->requirements)
            );
        }
        $routeAnnotation->setDefaults($defaults);
    }
}
