<?php

namespace Matze\Core\Console;

use Matze\Core\Core;
use Matze\Core\EventDispatcher\ClearCacheEvent;
use Matze\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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
			->setDescription('Clears the local cache');
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
			->name('*php');

		foreach ($finder as $file) {
			/** @var SplFileInfo $file */
			unlink($file->getPathname());
		}
		$output->writeln('<info>...done</info>');

		$output->write('Rebuild DIC...');
		Core::rebuildDIC();
		$output->writeln('<info>...done</info>');

		$output->write('Set permissions...');
		$file_system->chmod([
			'cache/',
			'cache/twig/',
			'logs/',
		] , 0777, 0000, true);
		$output->writeln('<info>...done</info>');

		$event = new ClearCacheEvent($output);
		$this->getEventDispatcher()->dispatch(ClearCacheEvent::CLEAR, $event);

	}

} 