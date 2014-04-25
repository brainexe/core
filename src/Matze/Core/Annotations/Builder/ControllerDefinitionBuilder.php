<?php

namespace Matze\Core\Annotations\Builder;

use Matze\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use Matze\Core\Annotations\Route;
use Matze\Core\DependencyInjection\CompilerPass\ControllerCompilerPass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Definition;

class ControllerDefinitionBuilder extends ServiceDefinitionBuilder {
	/**
	 * {@inheritdoc}
	 */
	public function build(\ReflectionClass $reflection_class, $annotation) {
		$definitionHolder = parent::build($reflection_class, $annotation);
		/** @var Definition $definition */
		$definition = $definitionHolder['definition'];

		$id = sprintf('Controller.%s', str_replace('Controller', '', $definitionHolder['id']));
		$definition->addTag(ControllerCompilerPass::TAG);

		return ['id' => $id, 'definition' => $definition];
	}

	/**
	 * @param ReflectionMethod[] $methods
	 * @param Definition $definition
	 */
	protected function processMethods($methods, Definition $definition) {
		parent::processMethods($methods, $definition);

		foreach ($methods as $method) {
			/** @var Route $route_annotation */
			if ($route_annotation = $this->reader->getMethodAnnotation($method, 'Symfony\Component\Routing\Annotation\Route')) {
				$defaults = $route_annotation->getDefaults();

				$class_parts = explode('\\', $definition->getClass());
				$class = str_replace('Controller', '', $class_parts[count($class_parts)-1]);
				$class = 'Controller.' . $class;

				$defaults['_controller'] = [$class, $method->getName()];
				$route_annotation->setDefaults($defaults);

				ControllerCompilerPass::addRoute($route_annotation);
			}
		}
	}
}