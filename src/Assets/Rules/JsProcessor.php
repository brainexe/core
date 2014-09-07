<?php

namespace Matze\Core\Assets\Rules;

use Assetic\Asset\FileAsset;
use Assetic\Filter\Yui\JsCompressorFilter;

/**
 * @Service("Assets.JsProcessor", public=false)
 */
class JsProcessor extends MergableProcessor {

	public function __construct() {
		parent::__construct('*.js');
	}

	/**
	 * {@inheritdoc}
	 */
	public function setFilterForAsset(FileAsset $asset, $relative_file_path) {
		if ($this->_debug) {
			return;
		}

		if($this->_yui_jar) {
			$asset->ensureFilter(new JsCompressorFilter($this->_yui_jar));
		}
	}

} 