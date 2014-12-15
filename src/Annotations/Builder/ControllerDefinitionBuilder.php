<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\DependencyInjection\CompilerPass\ControllerCompilerPass;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Definition;

class ControllerDefinitionBuilder extends ServiceDefinitionBuilder {
	/**
	 * {@inheritdoc}
	 */
	public function build(ReflectionClass $reflection_class, $annotation) {
		$definitionHolder = parent::build($reflection_class, $annotation);

		/** @var Definition $definition */
		$definition = $definitionHolder['definition'];

		$id = sprintf('__Controller.%s', str_replace('Controller', '', $definitionHolder['id']));
		$definition->addTag(ControllerCompilerPass::CONTROLLER_TAG);

		return [
			'id' => $id,
			'definition' => $definition
		];
	}

	/**
	 * @param ReflectionMethod[] $methods
	 * @param Definition $definition
	 */
	protected function _processMethods($methods, Definition $definition) {
		parent::_processMethods($methods, $definition);

		foreach ($methods as $method) {
			/** @var Route $route_annotation */
			$route_annotation = $this->_reader->getMethodAnnotation($method, Route::class);

			if ($route_annotation) {
				/** @var Guest $guest_annotation */
				$guest_annotation = $this->_reader->getMethodAnnotation($method, Guest::class);

				$class_parts = explode('\\', $definition->getClass());
				$class = str_replace('Controller', '', $class_parts[count($class_parts)-1]);
				$class = 'Controller.' . $class;

				$defaults = $route_annotation->getDefaults();
				$defaults['_controller'] = [$class, $method->getName()];
				if ($guest_annotation) {
					$defaults['_guest'] = true;
				}

				$route_annotation->setDefaults($defaults);

				$definition->addTag(ControllerCompilerPass::ROUTE_TAG, [$route_annotation]);
			}
		}
	}
}
