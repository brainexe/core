<?php

namespace Matze\Core\Annotations\Builder;

use Matze\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use Symfony\Component\DependencyInjection\Definition;

class EventListenerDefinitionBuilder extends ServiceDefinitionBuilder {
	/**
	 * {@inheritdoc}
	 */
	public function build(\ReflectionClass $reflClass, $annot) {
		$definitionHolder = parent::build($reflClass, $annot);
		/** @var Definition $definition */
		$definition = $definitionHolder['definition'];

		$definition->addTag('event_subscriber');

		return ['id' => $definitionHolder['id'], 'definition' => $definition];
	}
}