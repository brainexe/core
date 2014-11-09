<?php

namespace BrainExe\Core\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Annotations\Builder\EventListenerDefinitionBuilder;

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