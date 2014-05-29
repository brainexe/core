<?php

namespace Matze\Core\Console;

use Assetic\AssetWriter;
use Matze\Core\Assets\AssetCollector;
use Matze\Core\Assets\AssetUrl;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @Command
 */
class AssetsDumpCommand extends AbstractCommand {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('assets:dump')
			->setDescription('Build minified asset files');
	}

	/**
	 * @var AssetCollector
	 */
	private $_asset_collector;

	/**
	 * @Inject({"@AssetCollector"})
	 */
	public function __construct(AssetCollector $asset_collector) {
		$this->_asset_collector = $asset_collector;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$cache_dir = ROOT . 'web';
		exec(sprintf('rm -Rf %s/*', $cache_dir));
		copy(MATZE_VENDOR_ROOT.'core/scripts/web.php', ROOT.'web/index.php');

		$manager = $this->_asset_collector->collectAssets();

		$writer = new AssetWriter($cache_dir);
		$writer->writeManagerAssets($manager);

		if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
			foreach ($manager->getNames() as $name) {
				$asset_colector = $manager->get($name);
				$output->writeln($asset_colector->getTargetPath());
			}
		}

		$new_files = new Finder();
		$new_files
			->files()
			->in($cache_dir)
			->notName('*.php');

		$md5 = [];
		foreach ($new_files as $file) {
			/** @var SplFileInfo $file */
			$path = $file->getPathname();
			$md5[$file->getRelativePathname()] = substr(base_convert(md5_file($path), 16, 36), 0, 10);
		}

		$md5_file = sprintf('%s%s', ROOT, AssetUrl::HASH_FILE);
		$content = sprintf('<?php return %s;', var_export($md5, true));
		file_put_contents($md5_file, $content);
	}

} 
