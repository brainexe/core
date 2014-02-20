<?php

namespace Matze\Core\Annotations\Builder;

use Matze\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ControllerDefinitionBuilder extends ServiceDefinitionBuilder {
	/**
	 * {@inheritdoc}
	 */
	public function build(\ReflectionClass $reflClass, $annot) {
		$definitionHolder = parent::build($reflClass, $annot);
		/** @var Definition $definition */
		$definition = $definitionHolder['definition'];

		$id = sprintf('Controller.%s', str_replace('Controller', '', $definitionHolder['id']));
		$definition->setPublic(false);
		$definition->addTag('controller');

		return ['id' => $id, 'definition' => $definition];
	}
}