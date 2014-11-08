<?php

namespace BrainExe\Core\Application\SelfUpdate;

use BrainExe\Core\Traits\EventDispatcherTrait;
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
        $commands[] = 'git pull';
        $commands[] = 'php composer.phar update -o';

        $process = new Process(implode('&&', $commands));
		$process->setTimeout(0);
		
		$process->run(function ($type, $buffer) {
			unset($type);
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