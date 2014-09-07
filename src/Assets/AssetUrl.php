<?php

namespace Matze\Core\Assets;

/**
 * @Service(public=false)
 */
class AssetUrl {

	const HASH_FILE = 'cache/assets.php';
	const HASH_LENGTH = 12;

	/**
	 * @Inject("%cdn.url%")
	 * @param $cdn_url
	 */
	public function __construct($cdn_url) {
		$this->_cdn_url = $cdn_url;
	}

	/**
	 * @var string[]
	 */
	private $_hashes = null;

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

		list($name, $extension) = explode('.', $path, 2);

		return sprintf('%s%s-%s.%s', $this->_cdn_url, $name, $hash, $extension);
	}

	private function _initHashes() {
		if (null !== $this->_hashes) {
			return;
		}

		$file = ROOT . self::HASH_FILE;
		if (!is_file($file)) {
			$this->_hashes = [];
			return;
		}

		$this->_hashes = include $file;
	}
}
