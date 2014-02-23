<?php

namespace Matze\Core\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Matze\Annotations\Annotations\Service;
use Matze\Core\Annotations\Builder\EventListenerDefinitionBuilder;

/**
 * @Annotation
 */
class EventListener extends Service {
	/**
	 * {@inheritdoc}
	 */
	public static function getBuilder(Reader $reader) {
		return new EventListenerDefinitionBuilder($reader);
	}
} 