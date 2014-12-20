<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\DependencyInjection\CompilerPass\ControllerCompilerPass;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Definition;

class ControllerDefinitionBuilder extends ServiceDefinitionBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        $definitionHolder = parent::build($reflectionClass, $annotation);

        /** @var Definition $definition */
        $definition = $definitionHolder['definition'];

        $serviceId = sprintf('__Controller.%s', str_replace('Controller', '', $definitionHolder['id']));
        $definition->addTag(ControllerCompilerPass::CONTROLLER_TAG);

        return [
        'id' => $serviceId,
        'definition' => $definition
        ];
    }

    /**
     * @param ReflectionMethod[] $methods
     * @param Definition $definition
     */
    protected function _processMethods($methods, Definition $definition)
    {
        parent::_processMethods($methods, $definition);

        foreach ($methods as $method) {
            /** @var Route $routeAnnotation */
            $routeAnnotation = $this->_reader->getMethodAnnotation($method, Route::class);

            if ($routeAnnotation) {
                /** @var Guest $guestAnnotation */
                $guestAnnotation = $this->_reader->getMethodAnnotation($method, Guest::class);

                $classParts = explode('\\', $definition->getClass());
                $class = str_replace('Controller', '', $classParts[count($classParts)-1]);
                $class = 'Controller.' . $class;

                $defaults = $routeAnnotation->getDefaults();
                $defaults['_controller'] = [$class, $method->getName()];
                if ($guestAnnotation) {
                    $defaults['_guest'] = true;
                }

                $routeAnnotation->setDefaults($defaults);

                $definition->addTag(ControllerCompilerPass::ROUTE_TAG, [$routeAnnotation]);
            }
        }
    }
}
