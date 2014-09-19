<?php

namespace Matze\Core\Assets;

use Assetic\Asset\FileAsset;
use Matze\Core\Assets\Rules\CopyProcessor;
use Matze\Core\Assets\Rules\Processor;
use Matze\Core\Assets\Rules\MergableProcessor;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * @Service(public=false)
 */
class AssetCollector {

	const ASSETS_DIR = 'assets/';

	/**
	 * @var string
	 */
	private $_asset_dir;

	/**
	 * @var AssetManager
	 */
	private $_assetic;

	/**
	 * @var AssetCollectorLoader
	 */
	private $_asset_collector_loader;

	/**
	 * @Inject({"@Assetic", "@AssetCollectorLoader"})
	 * @param AssetManager $assetic
	 * @param AssetCollectorLoader $asset_collector_loader
	 */
	public function __construct(AssetManager $assetic, AssetCollectorLoader $asset_collector_loader) {
		$this->_assetic = $assetic;
		$this->_asset_collector_loader = $asset_collector_loader;
	}

	/**
	 * @return AssetManager|null
	 */
	public function collectAssets() {
		$this->_asset_dir = $asset_path = ROOT . self::ASSETS_DIR;

		if (!is_dir($this->_asset_dir)) {
			return null;
		}

		$processors = $this->_asset_collector_loader->loadProcessors();
		foreach ($processors as $processor) {
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
			->in($this->_asset_dir)
			->name($processor->file_expression);

		foreach ($finder as $file) {
			/** @var SplFileInfo $file */

			$relative_path_name = $file->getRelativePathname();

			foreach ($processor->files as $file_name => $file_definition) {
				// is file attched to any file?
				foreach ($file_definition->input_files as $idx => $file_regexp) {
					$file_regexp = str_replace('/', '\\/', $file_regexp);

					if (preg_match('/' . $file_regexp . '/', $relative_path_name)) {
						$merged_file_names[$file_name][$idx][] = $relative_path_name;
						continue 3;
					}
				}
			}

			if (empty($processor->fallback)) {
				throw new RuntimeException(sprintf("File not referenced and no fallback defined: %s", $relative_path_name));
			}

			$merged_file_names[$processor->fallback][1000][] = $relative_path_name;
		}


		foreach ($merged_file_names as $file_name => $files) {
			$collection = new MergedFileCollection();
			$collection->setTargetPath($file_name);

			$this->_assetic->set(md5($file_name), $collection);

			ksort($files);

			foreach ($files as $file_list) {
				foreach ($file_list as $relative_file_path) {
					$asset = new FileAsset($this->_asset_dir . $relative_file_path, [], dirname($this->_asset_dir . $relative_file_path));
					$collection->add($asset);
				}
			}
			$processor->setFilterForAsset($collection, $file_name);
		}
	}

	/**
	 * @param CopyProcessor $definition
	 */
	private function _handleCopyExtension(CopyProcessor $definition) {
		$finder = new Finder();
		$finder
			->files()
			->in($this->_asset_dir)
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
