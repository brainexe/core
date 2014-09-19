<?php

namespace Matze\Core\Assets\Rules;

use Assetic\Asset\AssetInterface;
use Matze\Core\Assets\Filters\StripWhiteSpaceHtmlFilter;

/**
 * @Service("Assets.TemplateProcessor")
 */
class TemplateProcessor extends CopyProcessor {
	public function __construct() {
		parent::__construct('*.{html}');
	}

	/**
	 * {@inheritdoc}
	 */
	public function setFilterForAsset(AssetInterface $asset, $relative_file_path) {
		$asset->ensureFilter(new StripWhiteSpaceHtmlFilter());
	}
} 