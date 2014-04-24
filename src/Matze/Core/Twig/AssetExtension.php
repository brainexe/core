<?php

namespace Matze\Core\Twig;
use Matze\Core\Assets\AssetUrl;

/**
 * @TwigExtension
 */
class AssetExtension extends \Twig_Extension {

	/**
	 * @var AssetUrl
	 */
	private $_asset_url;

	/**
	 * @Inject("@AssetUrl")
	 */
	public function __construct(AssetUrl $asset_url) {
		$this->_asset_url = $asset_url;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions() {
		return [
			'asset_url' => new \Twig_Function_Method($this, 'getAssetUrl', ['asset_url' => ['all']])
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