<?php

namespace Matze\Core\Assets\Rules;

use Assetic\Asset\AssetInterface;

abstract class Processor {

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
	 * @param AssetInterface $asset
	 * @param string $relative_file_path
	 */
	public function setFilterForAsset(AssetInterface $asset, $relative_file_path) {

	}

} 