<?php

namespace Matze\Core\Annotations\Builder;

use Matze\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use Matze\Core\DependencyInjection\ControllerCompilerPass;
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
}