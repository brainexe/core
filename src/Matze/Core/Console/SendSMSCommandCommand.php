<?php

namespace Matze\Core\Console;

use Matze\Core\SMS\SMSGateway;
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
			->addArgument('message', InputArgument::REQUIRED, 'message');
	}

	/**
	 * @var SMSGateway
	 */
	private $_model_sms_gateway;

	/**
	 * @Inject("@SMSGateway")
	 */
	public function setSmsGatewayMessage(SMSGateway $model) {
		$this->_model_sms_gateway = $model;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$number = $input->getArgument('number');
		$message = $input->getArgument('message');

		$result = $this->_model_sms_gateway->sendText($number, $message);

		$output->writeln(print_r($result, true));
	}

} 
