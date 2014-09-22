<?php

namespace BrainExe\Core\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Annotations\Builder\ControllerDefinitionBuilder;

/**
 * @Annotation
 */
class Controller extends Service {
	/**
	 * {@inheritdoc}
	 */
	public static function getBuilder(Reader $reader) {
		return new ControllerDefinitionBuilder($reader);
	}

}