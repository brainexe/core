<?php

namespace Matze\Core\Assets\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Matze\Core\Assets\AssetUrl;

/**
 * @Service("Filter.RewriteTemplateLoaderFilter")
 */
class RewriteTemplateLoaderFilter implements FilterInterface {

	/**
	 * @var AssetUrl
	 */
	private $_asset_url;

	/**
	 * @Inject("@AssetUrl")
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

		$content = preg_replace_callback('/(\/templates\/([a-z\.\-]+).html)/', function($part) {
			$template_name = ltrim($part[0], '/');
			return $this->_asset_url->getAssetUrl(ltrim($template_name, '/'));
		}, $content);
		$asset->setContent($content);
	}
}