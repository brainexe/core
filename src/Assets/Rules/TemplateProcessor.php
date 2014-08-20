<?php

namespace Matze\Core\Assets\Rules;
/**
 * @Service("Assets.TemplateProcessor", public=false)
 */
class TemplateProcessor extends CopyProcessor {
	public function __construct() {
		parent::__construct('*.{html}');
	}
} 