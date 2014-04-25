<?php

namespace Matze\Core\Assets;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\CssImportFilter;
use Assetic\Filter\Yui\CssCompressorFilter;
use Assetic\Filter\Yui\JsCompressorFilter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @todo rework
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
	 * @var string[]
	 */
	private $_separate_files;

	/**
	 * @var string
	 */
	private $_yui_jar;

	/**
	 * @Inject({"%assets.priorities%", "%assets.mergable%", "%assets.separate%", "%yui.jar%"})
	 */
	public function __construct($priorities, $mergable, $separate, $yui_jar) {
		$this->_priorities = $priorities;
		$this->_mergable = $mergable;
		$this->_separate_files = array_flip($separate);
		$this->_yui_jar = $yui_jar;
	}

	/**
	 * @param AssetManager $manager
	 */
	public function collectAssets(AssetManager $manager) {
		$asset_path = ROOT . 'assets';

		if (!is_dir($asset_path)) {
			return;
		}

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

			$asset = new FileAsset($file->getPathname(), [], dirname($file->getPathname()));

			// process each file separately
			switch ($extension) {
				case 'js':
					if (strpos($file->getFilename(), '.min.js') === false) {
						if ($this->_yui_jar) {
							$asset->ensureFilter(new JsCompressorFilter($this->_yui_jar));
						}
					}
					break;
				case 'css':
					$asset->setTargetPath('/');
					$asset->ensureFilter(new CssImportFilter());
					if ($this->_yui_jar) {
						$asset->ensureFilter(new CssCompressorFilter($this->_yui_jar));
					}
			}

			if ($this->_isMerged($extension) && !isset($this->_separate_files[$file->getRelativePathname()])) {
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
