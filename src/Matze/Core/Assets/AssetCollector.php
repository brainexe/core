<?php

namespace Matze\Core\Assets;

use Assetic\Asset\AssetCollection;

abstract class AssetCollector {

	/**
	 * @return AssetCollection[]
	 */
	abstract public function getAssets();
}