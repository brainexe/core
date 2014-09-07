<?php

namespace Matze\Core\Assets\Rules;

use Assetic\Asset\FileAsset;
use Matze\Core\Assets\Filters\StripWhiteSpaceHtmlFilter;

/**
 * @Service("Assets.TemplateProcessor", public=false)
 */
class TemplateProcessor extends CopyProcessor {
	public function __construct() {
		parent::__construct('*.{html}');
	}

	/**
	 * {@inheritdoc}
	 */
	public function setFilterForAsset(FileAsset $asset, $relative_file_path) {
		$asset->ensureFilter(new StripWhiteSpaceHtmlFilter());
	}
} 