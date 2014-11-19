<?php

namespace BrainExe\Core\Console;

use Crunch\Inotify\Event;
use Crunch\Inotify\Handler;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @Command
 */
class DebugCommand extends AbstractCommand {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('debug')
			->setDescription('debugcommand');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$finder = new Finder();
		$finder
			->directories()
			->in(ROOT . 'assets');

		$handler = new Handler;
		$events = Event::CREATE | Event::MODIFY | Event::MOVE | Event::MOVE_SELF | Event::DELETE | Event::DELETE_SELF;
		foreach ($finder as $file) {
			/** @var SplFileInfo $file */
			$handler->register(ROOT . 'assets/' . $file->getRelativePathname(), $events, function (Event $event, Handler $handler) {
			});
		}

		$handler->cyclicWait(5, function (Handler $handler, $count) use ($output){
			$output->write('.');
			if ($count) {
				$input = new ArrayInput(['command' => 'assets:dump']);
				$this->getApplication()->run($input, $output);
			}
		});
	}

} 
