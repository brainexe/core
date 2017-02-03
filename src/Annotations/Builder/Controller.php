<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Annotations\Service;
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
     * @param ReflectionClass $reflectionClass
     * @param ControllerAnnotation|Service $annotation
     * @param Definition $definition
     * @return array
     */
    public function build(ReflectionClass $reflectionClass, Service $annotation, Definition $definition)
    {
        /** @var Definition $definition */
        list ($serviceId, $definition) = parent::build(
            $reflectionClass,
            $annotation,
            $definition
        );
        $serviceId = sprintf('__controller.%s', $serviceId);

        $definition->addTag(ControllerCompilerPass::CONTROLLER_TAG);
        $definition->setPublic(true);
        $definition->setShared(false);

        foreach ($reflectionClass->getMethods() as $method) {
            /** @var Route $routeAnnotation */
            $routeAnnotation = $this->reader->getMethodAnnotation(
                $method,
                Route::class
            );

            if ($routeAnnotation) {
                $this->handleRouteAnnotation(
                    $annotation,
                    $method,
                    $routeAnnotation,
                    $serviceId,
                    $definition
                );
            }
        }

        return [$serviceId, $definition];
    }


    /**
     * @param Route $routeAnnotation
     * @param ReflectionMethod $method
     * @param string $serviceId
     * @param Guest $guestAnnotation
     * @param Role $roleAnnotation
     */
    protected function setDefaults(
        Route $routeAnnotation,
        ReflectionMethod $method,
        string $serviceId,
        Guest $guestAnnotation = null,
        Role $roleAnnotation = null
    ) {
        $defaults = $routeAnnotation->getDefaults();
        $defaults['_controller'] = [
            $serviceId,
            $method->getName()
        ];

        if ($guestAnnotation) {
            $defaults['_guest'] = true;
        }

        if ($roleAnnotation) {
            $defaults['_role'] = $roleAnnotation->role;
        }

        $routeAnnotation->setDefaults($defaults);
    }

    /**
     * @param ControllerAnnotation $annotation
     * @param ReflectionMethod $method
     * @param Route $routeAnnotation
     * @param string $serviceId
     * @param Definition $definition
     */
    private function handleRouteAnnotation(
        ControllerAnnotation $annotation,
        ReflectionMethod$method,
        Route $routeAnnotation,
        string $serviceId,
        Definition $definition
    ) {
        /** @var Guest $guestAnnotation */
        $guestAnnotation = $this->reader->getMethodAnnotation($method, Guest::class);

        /** @var Role $roleAnnotation */
        $roleAnnotation = $this->reader->getMethodAnnotation($method, Role::class);

        $this->setDefaults($routeAnnotation, $method, $serviceId, $guestAnnotation, $roleAnnotation);

        if ($annotation->requirements) {
            $routeAnnotation->setRequirements(
                array_merge(
                    $routeAnnotation->getRequirements(),
                    $annotation->requirements
                )
            );
        }

        $definition->addTag(ControllerCompilerPass::ROUTE_TAG, [$routeAnnotation]);
    }
}
