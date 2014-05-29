<?php

namespace Matze\Core\Assets\Rules;
/**
 * @Service("Assets.ImageProcessor", public=false)
 */
class ImageProcessor extends CopyProcessor {
	public function __construct() {
		parent::__construct('*.{jpg,png,gif,otf,oet,svg,woff,ttf}');
	}
} 