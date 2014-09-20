<?php

namespace Matze\Core\Console;

use Matze\Core\MessageQueue\MessageQueueWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class MessageQueueRunCommand extends Command {

	/**
	 * @var MessageQueueWorker
	 */
	private $_message_queue_worker;

	/**
	 * @Inject("@MessageQueueWorker")
	 * @param MessageQueueWorker $message_queue_worker
	 */
	public function __construct(MessageQueueWorker $message_queue_worker) {
		$this->_message_queue_worker = $message_queue_worker;
		parent::__construct(null);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('messagequeue:run')
			->setDescription('Runs message queue')
			->addArgument('timeout', InputArgument::OPTIONAL, 'Timeout', 0)
			->addArgument('interval', InputArgument::OPTIONAL, 'Interval', 2);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$timeout = $input->getArgument('timeout');
		$interval = $input->getArgument('interval');

		$this->_message_queue_worker->run($timeout, $interval);
	}
}