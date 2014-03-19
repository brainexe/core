<?php

namespace Matze\Core\Console;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Filter\CssRewriteFilter;
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
	 * @Inject("@Assetic")
	 */
	public function __construct(AssetManager $assetic) {
		$this->_assetic = $assetic;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$web_dir = ROOT . '/web/';
		$cache_dir = $web_dir . 'cache';

		$writer = new AssetWriter($cache_dir);
		$writer->writeManagerAssets($this->_assetic);
	}

} 
