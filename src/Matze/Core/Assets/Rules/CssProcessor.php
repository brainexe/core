<?php

namespace Matze\Core\Assets\Rules;
use Assetic\Asset\FileAsset;
use Assetic\Filter\Yui\CssCompressorFilter;

/**
 * @Service("Assets.CssProcessor", public=false)
 */
class CssProcessor extends MergableProcessor {

	public function __construct() {
		parent::__construct('*.css');
	}

	/**
	 * {@inheritdoc}
	 */
	public function setFilterForAsset(FileAsset $asset, $relative_file_path) {
		if ($this->_yui_jar && !$this->_debug) {
			$asset->ensureFilter(new CssCompressorFilter($this->_yui_jar));
		}
	}
} 