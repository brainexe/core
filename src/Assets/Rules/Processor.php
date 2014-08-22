<?php

namespace Matze\Core\Assets\Rules;

use Assetic\Asset\FileAsset;

class Processor {

	/**
	 * @var string
	 */
	public $file_expression;

	/**
	 * @param string $extension
	 */
	public function __construct($extension) {
		$this->file_expression = $extension;
	}

	/**
	 * @param FileAsset $asset
	 * @param string $relative_file_path
	 */
	public function setFilterForAsset(FileAsset $asset, $relative_file_path) {

	}

} 