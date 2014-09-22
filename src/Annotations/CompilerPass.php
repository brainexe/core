<?php

namespace BrainExe\Core\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Annotations\Builder\CompilerPassDefinitionBuilder;

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