<?php

namespace Matze\Core\Application\SelfUpdate;

use Matze\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Process\Process;

/**
 * @Service
 */
class SelfUpdate {

	use EventDispatcherTrait;

	/**
	 * @return void
	 */
	public function startUpdate() {
        $commands = [];
        $commands[] = sprintf('cd %s', ROOT);
        $commands[] = 'git pull --force';
        $commands[] = 'php composer.phar update -o';

        $process = new Process(implode('&&', $commands));

		$process->run(function ($type, $buffer) {
			$event = new SelfUpdateEvent(SelfUpdateEvent::PROCESS);
			$event->payload = $buffer;

			$this->dispatchEvent($event);
		});

		if ($process->isSuccessful()) {
			$event = new SelfUpdateEvent(SelfUpdateEvent::DONE);
			$event->payload = $process->getOutput();
		} else {
			$event = new SelfUpdateEvent(SelfUpdateEvent::ERROR);
			$event->payload = $process->getErrorOutput();
		}

		$this->dispatchEvent($event);
    }
} 