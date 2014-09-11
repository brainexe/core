<?php

namespace Matze\Core\Assets\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Matze\Core\Assets\AssetUrl;

/**
 * @service("Filter.RewriteCssFilters")
 */
class RewriteCssFilters implements FilterInterface {

	/**
	 * @var AssetUrl
	 */
	private $_asset_url;

	/**
	 * @Inject("@AssetUrl")
	 * @param AssetUrl $assetUrl
	 */
	public function __construct(AssetUrl $assetUrl) {
		$this->_asset_url = $assetUrl;
	}

	/**
	 * {@inheritdoc}
	 */
	public function filterLoad(AssetInterface $asset) {
	}

	/**
	 * {@inheritdoc}
	 */
	public function filterDump(AssetInterface $asset) {
		$content = $asset->getContent();

		$content = preg_replace_callback('/([\d\w\.\-\/]+)\.(jpg|png|gif|otf|oet|svg|woff|ttf)/', function($part) {
			$asset = ltrim($part[0], './');
			return $this->_asset_url->getAssetUrl($asset);
		}, $content);

		$asset->setContent($content);
	}
}