<?php

namespace Matze\Core\Console;

use Assetic\AssetManager;
use Assetic\AssetWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @Command
 */
class AssetsDumpCommand extends Command {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('assets:dump')
			->setDescription('Dump Assets');
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
	protected function execute(InputInterface $input, OutputInterface $output) {
		foreach ($this->_assetic->getNames() as $name) {
			$asset_collection = $this->_assetic->get($name);
//			$asset_collection->dump();
		}


		$writer = new AssetWriter(ROOT . 'web/cache/');
		$writer->writeManagerAssets($this->_assetic);
	}

} 
