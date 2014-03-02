<?php

namespace Matze\Core\Console;

use NexmoMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @Command
 */
class SendSMSCommandCommand extends Command {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('sms:sens')
			->setDescription('Send SMS')
			->addArgument('number', InputArgument::REQUIRED, 'Cellphone number')
			->addArgument('text', InputArgument::REQUIRED, 'tex')
			->addArgument('from', InputArgument::OPTIONAL, 'From', 'App');
	}

	/**
	 * @var NexmoMessage
	 */
	private $_model_nexmo_message;

	/**
	 * @Inject("@NexmoMessage")
	 */
	public function setNexmoMessage(NexmoMessage $model) {
		$this->_model_nexmo_message = $model;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$result = $this->_model_nexmo_message->sendText($input->getArgument('number'), $input->getArgument('from'),$input->getArgument('text'));

		$output->write(print_r($result, true));
	}

} 
