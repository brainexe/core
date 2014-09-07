<?php

namespace Matze\Core\Console;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use Crunch\Inotify\Handler;
use Crunch\Inotify\Event;

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

		$handler = new Handler;
		$handler->register(ROOT . 'assets', Event::ALL ^ (Event::ACCESS | Event::OPEN), function (Event $event, Handler $handler) {
			var_dump($event->name);
		});

		$handler->cyclicWait(5, function (Handler $handler, $count) use ($output){
			echo $count."\n";

			if ($count) {
				$input = new ArrayInput(['command' => 'assets:dump']);
				$this->getApplication()->run($input, $output);
			}
		});
	}

} 
