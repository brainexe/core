<?php

namespace Matze\Core\Assets;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @Service(public=false)
 */
class AssetCollector {

	/**
	 * @var string[]
	 */
	private $_priorities;

	/**
	 * @var string[]
	 */
	private $_mergable;

	/**
	 * @Inject({"%assets_priorities%", "%assets_mergable%"})
	 */
	public function __construct($priorities, $mergable) {
		$this->_priorities = $priorities;
		$this->_mergable = $mergable;
	}

	/**
	 * @param AssetManager $manager
	 */
	public function collectAssets(AssetManager $manager) {
		$asset_path = ROOT . 'assets';

		$finder = new Finder();
		$finder
			->files()
			->in($asset_path)
			->sort(function(SplFileInfo $a, SplFileInfo $b) {
				$a_idx = array_search($a->getRelativePathname(), $this->_priorities);
				if ($a_idx === false) {
					return 1;
				}
				$b_idx = array_search($b->getRelativePathname(), $this->_priorities);
				if ($b_idx === false) {
					return -1;
				}

				return $a_idx > $b_idx;
			});

		foreach ($finder as $file) {
			/** @var SplFileInfo $file */

			$extension = $file->getExtension();

			$asset = new FileAsset($file->getPathname());

			if ($this->_isMerged($extension)) {
				if (!$manager->has($extension)) {
					$collection = new AssetCollection();
					$collection->setTargetPath('merged.' . $extension);
					$manager->set($extension, $collection);
				} else {
					$collection = $manager->get($extension);
				}
				$collection->add($asset);
			} else {
				$asset->setTargetPath($file->getRelativePathname());
				$manager->set(md5($file->getPathname()), $asset);
			}
		}
	}

	/**
	 * @param string $extension
	 * @return boolean
	 */
	private function _isMerged($extension) {
		return in_array($extension, $this->_mergable);
	}
}
