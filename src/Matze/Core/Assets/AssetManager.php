<?php

namespace Matze\Core\Assets;

use Assetic\Asset\AssetCollection;

class AssetManager extends \Assetic\AssetManager {

	/**
	 * @param AssetCollector $collector
	 */
	public function addAssetCollection(AssetCollector $collector) {
		foreach ($collector->getAssets() as $name => $assets) {
			/** @var AssetCollection $existing */
			$existing = $this->get($name);
			$existing->add($assets);
		}
	}
} 