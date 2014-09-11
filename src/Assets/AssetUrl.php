<?php

namespace Matze\Core\Assets;

/**
 * @Service(public=false)
 */
class AssetUrl {

	const HASH_FILE = 'cache/assets.php';
	const HASH_LENGTH = 6;

	/**
	 * @var string[]
	 */
	private $_hashes = [];

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
	public function getHash($path) {
		$this->_initHashes();

		if (!empty($this->_hashes[$path])) {
			return $this->_hashes[$path];
		}

		return null;
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public function getAssetUrl($path) {
		$hash = $this->getHash($path);

		if (empty($hash)) {
			return sprintf('%s%s', $this->_cdn_url, $path);
		} else {
			list($name, $extension) = explode('.', $path, 2);
			return sprintf('%s%s-%s.%s', $this->_cdn_url, $name, $hash, $extension);
		}
	}

	/**
	 * @param string $path
	 * @param string $hash
	 */
	public function addHash($path, $hash) {
		$this->_hashes[$path] = $hash;
	}

	private function _initHashes() {
		if ($this->_initialized) {
			return;
		}

		$file = ROOT . self::HASH_FILE;
		if (!is_file($file)) {
			$this->_hashes = [];
			return;
		}
		$this->_initialized = true;

		$this->_hashes = include $file;
	}
}
