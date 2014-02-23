<?php

namespace Matze\Core\Annotations\Builder;

use Matze\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use Matze\Core\DependencyInjection\TwigExtensionCompilerPass;
use Symfony\Component\DependencyInjection\Definition;

class TwigExtensionDefinitionBuilder extends ServiceDefinitionBuilder {
	/**
	 * {@inheritdoc}
	 */
	public function build(\ReflectionClass $reflection_class, $annotation) {
		$definitionHolder = parent::build($reflection_class, $annotation);
		/** @var Definition $definition */
		$definition = $definitionHolder['definition'];

		$definition->addTag(TwigExtensionCompilerPass::TAG);
		$definition->setPublic(false);

		return ['id' => $definitionHolder['id'], 'definition' => $definition];
	}
}