<?php

namespace BrainExe\Core\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Annotations\Builder\MiddlewareDefinitionBuilder;

/**
 * @Annotation
 */
class Middleware extends Service {
	public $priority = 5;

	/**
	 * {@inheritdoc}
	 */
	public static function getBuilder(Reader $reader) {
		return new MiddlewareDefinitionBuilder($reader);
	}
}