<?php

namespace BrainExe\Core\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Annotations\Builder\TwigExtensionDefinitionBuilder;

/**
 * @Annotation
 */
class TwigExtension extends Service {

	/**
	 * @var boolean
	 */
	public $compiler = false;

	/**
	 * {@inheritdoc}
	 */
	public static function getBuilder(Reader $reader) {
		return new TwigExtensionDefinitionBuilder($reader);
	}
} 