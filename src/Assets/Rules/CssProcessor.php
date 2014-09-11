<?php

namespace Matze\Core\Assets\Rules;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\Yui\CssCompressorFilter;
use Matze\Core\Traits\ServiceContainerTrait;

/**
 * @Service("Assets.CssProcessor", public=false)
 */
class CssProcessor extends MergableProcessor {

	use ServiceContainerTrait;

	public function __construct() {
		parent::__construct('*.css');
	}

	/**
	 * {@inheritdoc}
	 */
	public function setFilterForAsset(AssetInterface $asset, $relative_file_path) {
		$asset->ensureFilter($this->getService('Filter.RewriteCssFilters'));

		if ($this->_debug) {
			return;
		}

		if ($this->_yui_jar) {
			$asset->ensureFilter(new CssCompressorFilter($this->_yui_jar));
		}
	}

}