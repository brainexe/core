<?php

namespace Matze\Core\Console;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\AssetWriter;
use Matze\Core\Assets\AssetCollector;
use Matze\Core\Assets\AssetUrl;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @Command
 */
class AssetsDumpCommand extends AbstractCommand {
	/**
	 * @var AssetUrl
	 */
	private $_asset_url;

	/**
	 * @var AssetCollector
	 */
	private $_asset_collector;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('assets:dump')
			->setDescription('Build minified asset files');
	}

	/**
	 * @Inject({"@AssetCollector", "@AssetUrl"})
	 * @param AssetCollector $asset_collector
	 * @param AssetUrl $asset_url
	 */
	public function __construct(AssetCollector $asset_collector, AssetUrl $asset_url) {
		$this->_asset_collector = $asset_collector;
		$this->_asset_url = $asset_url;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$cache_dir = ROOT . 'web';
		exec(sprintf('rm -Rf %s/*', $cache_dir));
		copy(MATZE_VENDOR_ROOT . 'core/scripts/web.php', ROOT . 'web/index.php');

		$manager = $this->_asset_collector->collectAssets();

		$writer = new AssetWriter($cache_dir);
		$md5s = [];

		foreach ($manager->getNames() as $name) {
			$asset_colector = $manager->get($name);

			$asset_colector->load();

			// calculate md5 sum of source content and rename
			$md5 = substr(md5($asset_colector->getContent()), 0, AssetUrl::HASH_LENGTH);
			$relative_file_path = $asset_colector->getTargetPath();
			$md5s[$relative_file_path] = $md5;
			$this->_asset_url->addHash($asset_colector->getTargetPath(), $md5);
			$asset_colector->setTargetPath(preg_replace('/.(\w*)$/', '-' . $md5 . '.$1', $relative_file_path));

			echo $relative_file_path."\n";
			$writer->writeAsset($asset_colector);

			if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
				$target_path = $asset_colector->getTargetPath();
				$output->writeln("<info>$target_path</info>");

				if ($asset_colector instanceof AssetCollection) {
					foreach ($asset_colector->all() as $asset) {
						/** @var FileAsset $asset */
						$source_file = $asset->getSourceDirectory() . '/' . $asset->getSourcePath();
						$file_size = filesize($source_file);
						$output->writeln(sprintf('->%s (%2.1fkb)', $asset->getSourcePath(), $file_size / 1000));
					}
				}

				$file_size = filesize($cache_dir . '/' . $target_path);
				$output->writeln(sprintf("<info>->%2.1fkb</info>\n", $file_size / 1000));
			}
		}

		// save asset hashs
		$md5_file = sprintf('%s%s', ROOT, AssetUrl::HASH_FILE);
		$content = sprintf('<?php return %s;', var_export($md5s, true));
		file_put_contents($md5_file, $content);
	}
}
