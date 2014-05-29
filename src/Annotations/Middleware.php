<?php

namespace Matze\Core\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Matze\Annotations\Annotations\Service;
use Matze\Core\Annotations\Builder\MiddlewareDefinitionBuilder;

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