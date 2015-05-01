<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Loader\Annotation\ServiceDefinitionBuilder;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Role;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\DependencyInjection\CompilerPass\ControllerCompilerPass;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Definition;

class Controller extends ServiceDefinitionBuilder
{

    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        /** @var Definition $definition */
        list(, $definition) = parent::build(
            $reflectionClass,
            $annotation
        );

        $definition->addTag(ControllerCompilerPass::CONTROLLER_TAG);

        return [
            $this->getServiceId(),
            $definition
        ];
    }

    /**
     * @param ReflectionMethod[] $methods
     * @param Definition $definition
     */
    protected function processMethods($methods, Definition $definition)
    {
        parent::processMethods($methods, $definition);

        foreach ($methods as $method) {
            /** @var Route $routeAnnotation */
            $routeAnnotation = $this->reader->getMethodAnnotation($method, Route::class);

            if ($routeAnnotation) {
                /** @var Guest $guestAnnotation */
                $guestAnnotation = $this->reader->getMethodAnnotation($method, Guest::class);

                /** @var Role $roleAnnotation */
                $roleAnnotation = $this->reader->getMethodAnnotation($method, Role::class);

                $defaults = $routeAnnotation->getDefaults();
                $defaults['_controller'] = [
                    $this->getServiceId(),
                    $method->getName()
                ];
                if ($guestAnnotation) {
                    $defaults['_guest'] = true;
                }

                if ($roleAnnotation) {
                    $defaults['_role'] = $roleAnnotation->role;
                }

                $routeAnnotation->setDefaults($defaults);

                $definition->addTag(ControllerCompilerPass::ROUTE_TAG, [$routeAnnotation]);
            }
        }
    }

    /**
     * @return string
     */
    private function getServiceId()
    {
        return sprintf(
            '__Controller.%s',
            str_replace('Controller', '', $this->serviceId)
        );
    }
}
