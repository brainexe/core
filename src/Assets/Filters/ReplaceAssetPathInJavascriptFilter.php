<?php

namespace Matze\Core\Assets\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Matze\Core\Assets\AssetUrl;

/**
 * @Service("Filter.ReplaceAssetPathInJavascriptFilter")
 */
class ReplaceAssetPathInJavascriptFilter implements FilterInterface {

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

		$content = preg_replace_callback('/asset\([\'"](([\w\d\.\-\/]+)\.(html|js))[\'"]\)/', function($part) {
			$template_name = ltrim($part[1], '/');
			$new_path = $this->_asset_url->getAssetUrl(ltrim($template_name, '/'));
			return sprintf("'%s'", $new_path);
		}, $content);
		$asset->setContent($content);
	}
}