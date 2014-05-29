<?php

namespace Matze\Core\Annotations\Builder;

use Matze\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use Matze\Core\Annotations\TwigExtension;
use Matze\Core\DependencyInjection\CompilerPass\TwigExtensionCompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class TwigExtensionDefinitionBuilder extends ServiceDefinitionBuilder {

	/**
	 * @param ReflectionClass $reflection_class
	 * @param TwigExtension $annotation
	 * @return array
	 */
	public function build(ReflectionClass $reflection_class, $annotation) {
		$definitionHolder = parent::build($reflection_class, $annotation);
		/** @var Definition $definition */
		$definition = $definitionHolder['definition'];

		$definition->addTag(TwigExtensionCompilerPass::TAG, ['compiler' => $annotation->compiler]);
		$definition->setPublic(false);

		return ['id' => $definitionHolder['id'], 'definition' => $definition];
	}
}