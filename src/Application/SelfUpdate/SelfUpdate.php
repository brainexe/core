<?php

namespace BrainExe\Core\Application\SelfUpdate;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Service(public=false)
 */
class SelfUpdate {

	use EventDispatcherTrait;

	/**
	 * @var ProcessBuilder
	 */
	private $processBuilder;

	/**
	 * @inject("@ProcessBuilder")
	 * @param ProcessBuilder $processBuilder
	 */
	public function __construct(ProcessBuilder $processBuilder) {
		$this->processBuilder = $processBuilder;
	}

	/**
	 * @return void
	 */
	public function startUpdate() {
        $commands = [];
        $commands[] = sprintf('cd %s', ROOT);
        $commands[] = 'git pull';
        $commands[] = 'php composer.phar update -o';

		$process = $this->processBuilder
			->setArguments([implode('&&', $commands)])
			->setTimeout(0)
			->getProcess();

		$process->run(function($type, $buffer) {
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