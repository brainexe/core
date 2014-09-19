<?php

namespace Matze\Core\Assets\Rules;

use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Filter\GoogleClosure\CompilerJarFilter;
use Assetic\Filter\Yui\JsCompressorFilter;
use Matze\Core\Assets\Filters\ReplaceAssetPathInJavascriptFilter;
use Matze\Core\Assets\MergedFileCollection;
use Matze\Core\Traits\ServiceContainerTrait;

/**
 * @Service("Assets.JsProcessor")
 */
class JsProcessor extends MergableProcessor {

	use ServiceContainerTrait;

	/**
	 * @var string
	 */
	protected $_closure_jar;

	/**
	 * @Inject("%closure.jar%")
	 * @param string $closure
	 */
	public function setYar($closure) {
		$this->_closure_jar = $closure;
	}

	public function __construct() {
		parent::__construct('*.js');
	}

	/**
	 * {@inheritdoc}
	 */
	public function setFilterForAsset(AssetInterface $asset, $relative_file_path) {
		$asset->ensureFilter($this->getService('Filter.ReplaceAssetPathInJavascriptFilter'));

		if ($this->_debug) {
			return;
		}

		$already_minified = true;

		if ($asset instanceof MergedFileCollection) {
			foreach ($asset->all() as $file) {
				/** @var FileAsset $file */
				if (substr($file->getSourcePath(), -7, 7) !== '.min.js') {
					$already_minified = false;
					break;
				}
			}
		}

		if ($already_minified) {
			return;
		}

		if ($this->_closure_jar) {
			$asset->ensureFilter(new CompilerJarFilter($this->_closure_jar));
		} elseif($this->_yui_jar) {
			$asset->ensureFilter(new JsCompressorFilter($this->_yui_jar));
		}
	}

} 