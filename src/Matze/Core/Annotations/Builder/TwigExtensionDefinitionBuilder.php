<?php

namespace Matze\Core\Annotations\Builder;

use Matze\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use Symfony\Component\DependencyInjection\Definition;

class TwigExtensionDefinitionBuilder extends ServiceDefinitionBuilder {
	/**
	 * {@inheritdoc}
	 */
	public function build(\ReflectionClass $reflClass, $annot) {
		$definitionHolder = parent::build($reflClass, $annot);
		/** @var Definition $definition */
		$definition = $definitionHolder['definition'];

		$definition->addTag('twig_extension');

		return ['id' => $definitionHolder['id'], 'definition' => $definition];
	}
}