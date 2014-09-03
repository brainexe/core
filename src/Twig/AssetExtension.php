<?php

namespace Matze\Core\Twig;

use Matze\Core\Assets\AssetUrl;
use Twig_Extension;

/**
 * @TwigExtension
 */
class AssetExtension extends Twig_Extension {

	/**
	 * @var AssetUrl
	 */
	private $_asset_url;

	/**
	 * @Inject("@AssetUrl")
	 * @param AssetUrl $asset_url
	 */
	public function __construct(AssetUrl $asset_url) {
		$this->_asset_url = $asset_url;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions() {
		return [
			'asset_url' => new \Twig_Function_Method($this, 'getAssetUrl', ['is_safe' => ['all']])
		];
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public function getAssetUrl($path) {
		return $this->_asset_url->getAssetUrl($path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'asset';
	}
}