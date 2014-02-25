<?php

namespace Matze\Core\Annotations\Builder;

use Matze\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use Matze\Core\Annotations\CompilerPass;
use Matze\Core\DependencyInjection\GlobalCompilerPass;
use Symfony\Component\DependencyInjection\Definition;

class CompilerPassDefinitionBuilder extends ServiceDefinitionBuilder {
	/**
	 * @param \ReflectionClass $reflection_class
	 * @param CompilerPass $annotation
	 * @return array
	 */
	public function build(\ReflectionClass $reflection_class, $annotation) {
		$definitionHolder = parent::build($reflection_class, $annotation);

		/** @var Definition $definition */
		$definition = $definitionHolder['definition'];

		$definition->setPublic(false);
		$definition->addTag(GlobalCompilerPass::TAG, ['priority' => $annotation->priority]);

		return ['id' => $definitionHolder['id'], 'definition' => $definition];
	}
}