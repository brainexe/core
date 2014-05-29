<?php

namespace Matze\Core\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Matze\Annotations\Annotations\Service;
use Matze\Core\Annotations\Builder\TwigExtensionDefinitionBuilder;

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