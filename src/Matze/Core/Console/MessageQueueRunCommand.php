<?php

namespace Matze\Core\Console;

use Matze\Core\MessageQueue\MessageQueueWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;
use Matze\Annotations\Annotations as DI;

/**
 * @DI\Command
 */
class MessageQueueRunCommand extends Command {

	/**
	 * @var MessageQueueWorker
	 */
	private $_message_queue_worker;

	/**
	 * @DI\Inject("@MessageQueueWorker")
	 */
	public function setMessageQueueWorker(MessageQueueWorker $message_queue_worker) {
		$this->_message_queue_worker = $message_queue_worker;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('messagequeue:run')
			->setDescription('Runs message queue');;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->_message_queue_worker->run();
	}
}