<?php

namespace Matze\Core\Assets;

/**
 * @Service(public=false)
 */
class AssetUrl {

	const HASH_FILE = 'cache/assets.php';
	const HASH_LENGTH = 12;

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

		$base_path = '/';

		return sprintf('%s%s?%s', $base_path, $path, $hash);
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
