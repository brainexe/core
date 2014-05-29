<?php

namespace Matze\Core\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Matze\Annotations\Annotations\Service;
use Matze\Core\Annotations\Builder\CompilerPassDefinitionBuilder;

/**
 * @Annotation
 */
class CompilerPass extends Service {
	public $priority = 1;

	/**
	 * {@inheritdoc}
	 */
	public static function getBuilder(Reader $reader) {
		return new CompilerPassDefinitionBuilder($reader);
	}
}