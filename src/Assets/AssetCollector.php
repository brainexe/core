<?php

namespace Matze\Core\Assets;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Matze\Core\Assets\Rules\CopyProcessor;
use Matze\Core\Assets\Rules\Processor;
use Matze\Core\Assets\Rules\MergableProcessor;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @Service(public=false)
 */
class AssetCollector {

	/**
	 * @var Processor[]
	 */
	private $_processors;

	/**
	 * @var string
	 */
	private $_assetPath;

	/**
	 * @var AssetManager
	 */
	private $_assetic;

	/**
	 * @Inject("@Assetic")
	 */
	public function __construct(AssetManager $assetic) {
		$this->_assetic = $assetic;
	}

	/**
	 * @param Processor $processor
	 */
	public function addProcessor(Processor $processor) {
		$this->_processors[] = $processor;
	}

	/**
	 * @return AssetManager|null
	 */
	public function collectAssets() {
		$this->_assetPath = $asset_path = ROOT . 'assets';

		if (!is_dir($this->_assetPath)) {
			return null;
		}

		foreach ($this->_processors as $processor) {
			if ($processor instanceof MergableProcessor) {
				$this->_handleMergableExtension($processor);
			} else
				if ($processor instanceof CopyProcessor) {
				$this->_handleCopyExtension($processor);
			}
		}

		return $this->_assetic;
	}

	/**
	 * @param MergableProcessor $processor
	 */
	private function _handleMergableExtension(MergableProcessor $processor) {
		$merged_file_names = [];

		$finder = new Finder();
		$finder
			->files()
			->in($this->_assetPath)
			->name($processor->file_expression);

		foreach ($finder as $file) {
			/** @var SplFileInfo $file */

			$relative_path_name = $file->getRelativePathname();

			foreach ($processor->files as $file_name => $file_definition) {
				// is file attched to any file?
				foreach ($file_definition->input_files as $idx => $file_regexp) {
					if (preg_match('/' . ($file_regexp) . '/', $relative_path_name)) {
						$merged_file_names[$file_name][$idx][] = $relative_path_name;
						continue 3;
					}
				}
			}
			$merged_file_names[$processor->fallback][1000][] = $relative_path_name;
		}

		foreach ($merged_file_names as $file_name => $files) {
			$collection = new AssetCollection();
			$collection->setTargetPath($file_name);

			$this->_assetic->set(md5($file_name), $collection);

			ksort($files);

			foreach ($files as $file_list) {
				foreach ($file_list as $relative_file_path) {
					$asset = new FileAsset(ROOT . 'assets/' . $relative_file_path, [], dirname(ROOT . 'assets/' . $relative_file_path)); //TODO prefix?
					$collection->add($asset);

					$processor->setFilterForAsset($asset, $relative_file_path);
				}
			}
		}
	}

	/**
	 * @param CopyProcessor $definition
	 */
	private function _handleCopyExtension(CopyProcessor $definition) {
		$finder = new Finder();
		$finder
			->files()
			->in($this->_assetPath)
			->name($definition->file_expression);

		foreach ($finder as $file) {
			/** @var SplFileInfo $file */
			$relative_file_path = $file->getRelativePathname();

			$asset = new FileAsset($file->getPathname(), [], dirname($file->getPathname()));
			$asset->setTargetPath($relative_file_path);

			$definition->setFilterForAsset($asset, $relative_file_path);

			$this->_assetic->set(md5($file->getPathname()), $asset);
		}
	}
}
