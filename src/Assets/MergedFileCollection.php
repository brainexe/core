<?php

namespace Matze\Core\Assets;

use Assetic\Asset\AssetCollection;
use Assetic\Filter\FilterCollection;
use Assetic\Filter\FilterInterface;

class MergedFileCollection extends AssetCollection {

	/**
	 * {@inheritdoc}
	 * @todo
	 */
	public function dump(FilterInterface $additionalFilter = null) {

		$this->load($additionalFilter);
		(new FilterCollection($this->getFilters()))->filterDump($this);

		return $this->getContent();
	}
}