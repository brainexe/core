<?php

namespace Matze\Core\Console;

use Matze\Core\MessageQueue\MessageQueueWorker;
use Raspberry\Radio\RadioJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

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
			->addArgument('timeout', InputArgument::OPTIONAL, 'Timeout', 0);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->_message_queue_worker->run($input->getArgument('timeout'));
	}
}