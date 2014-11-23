<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\Core;
use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @Command
 */
class ClearCacheCommand extends Command {

	use EventDispatcherTrait;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('cache:clear')
			->setDescription('Clears the local cache')
			->setAliases(['cc']);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$file_system = new Filesystem();

		$output->write('Clear Cache...');

		$finder = new Finder();
		$finder
			->files()
			->in(ROOT . '/cache')
			->name('*.php')
			->notName('assets.php');

		foreach ($finder as $file) {
			/** @var SplFileInfo $file */
			unlink($file->getPathname());
		}
		$output->writeln('<info>done</info>');

		$output->write('Rebuild DIC...');

		Core::rebuildDIC();

		$output->writeln('<info>done</info>');

		$output->write('Set permissions...');
		$file_system->chmod([
			'cache/',
			'cache/',
			'logs/',
		] , 0777, 0000, true);
		$output->writeln('<info>done</info>');

		$input = new ArrayInput(['command' => 'templates:compile']);
		$this->getApplication()->run($input, $output);

		$input = new ArrayInput(['command' => 'translation:compile']);
		$this->getApplication()->run($input, $output);

		$input = new ArrayInput(['command' => 'redis:scripts:load']);
		$this->getApplication()->run($input, $output);

		$event = new ClearCacheEvent($this->getApplication(), $input, $output);
		$this->dispatchEvent($event);
	}

} 