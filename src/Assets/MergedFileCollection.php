<?php

namespace Matze\Core\Assets;

use Assetic\Asset\AssetCollection;
use Assetic\Filter\FilterCollection;
use Assetic\Filter\FilterInterface;

class MergedFileCollection extends AssetCollection {

	/**
	 * {@inheritdoc}
	 */
	public function dump(FilterInterface $additionalFilter = null) {
		$this->load($additionalFilter);

		$filters = new FilterCollection($this->getFilters());
		$filters->filterDump($this);

		return $this->getContent();
	}
}