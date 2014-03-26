<?php

namespace Matze\Core\Console;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\AssetWriter;
use Assetic\Filter\CssRewriteFilter;
use Matze\Core\Assets\AssetCollector;
use Matze\Core\Assets\AssetManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

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
	 * @var AssetManager
	 */
	private $_assetic;

	/**
	 * @var AssetCollector
	 */
	private $asset_collector;

	/**
	 * @Inject({"@Assetic", "@AssetCollector"})
	 */
	public function __construct(AssetManager $assetic, AssetCollector $asset_collector) {
		$this->_assetic = $assetic;
		$this->asset_collector = $asset_collector;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$cache_dir = ROOT . 'web/cache';

		$this->asset_collector->collectAssets($this->_assetic);

		$writer = new AssetWriter($cache_dir);
		$writer->writeManagerAssets($this->_assetic);

		if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
			foreach ($this->_assetic->getNames() as $name) {
				$asset_colector = $this->_assetic->get	($name);
				$output->writeln($asset_colector->getTargetPath());
			}
		}
	}

} 
