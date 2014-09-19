<?php

namespace Matze\Core\Assets\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class StripWhiteSpaceHtmlFilter implements FilterInterface {

	/**
	 * {@inheritdoc}
	 */
	public function filterLoad(AssetInterface $asset) {
	}

	/**
	 * Filters an asset just before it's dumped.
	 *
	 * @param AssetInterface $asset An asset
	 */
	public function filterDump(AssetInterface $asset) {
		$content = $asset->getContent();

		$content = str_replace("\t", '', $content);
		$content = preg_replace('/>\s+</m', '><', $content);
		$content = preg_replace('/<!--[^>]*-->/', '', $content);
		$content = preg_replace("/ +}}/", '}}', $content);
		$content = preg_replace("/{{ +/", '{{', $content);

		$asset->setContent($content);
	}
}