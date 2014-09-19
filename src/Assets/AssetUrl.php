<?php

namespace Matze\Core\Assets;

/**
 * @Service(public=false)
 */
class AssetUrl {

	const ASSET_FILE = 'cache/assets.php';
	const HASH_LENGTH = 8;

	/**
	 * @var string[]
	 */
	private $_asset_files = [];

	/**
	 * @var boolean
	 */
	private $_initialized = false;

	/**
	 * @Inject("%cdn.url%")
	 * @param string $cdn_url
	 */
	public function __construct($cdn_url) {
		$this->_cdn_url = $cdn_url;
	}

	/**
	 * @param string $path
	 * @return null|string
	 */
	public function getTargetUrl($path) {
		$this->_initFile();

		if (!empty($this->_asset_files[$path])) {
			return $this->_asset_files[$path];
		}

		return null;
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public function getAssetUrl($path) {
		$target_url = $this->getTargetUrl($path);

		return sprintf('%s%s', $this->_cdn_url, $target_url ?: $path);
	}

	/**
	 * @param string $source_file
	 * @param string $target_file
	 */
	public function addTargetUrl($source_file, $target_file) {
		$this->_asset_files[$source_file] = $target_file;
	}

	private function _initFile() {
		if ($this->_initialized) {
			return;
		}

		$file = ROOT . self::ASSET_FILE;
		if (!is_file($file)) {
			$this->_asset_files = [];
			return;
		}
		$this->_initialized = true;

		$this->_asset_files = include $file;
	}
}
