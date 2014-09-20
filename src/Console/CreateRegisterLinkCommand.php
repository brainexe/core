<?php

namespace Matze\Core\Console;

use Matze\Core\Authentication\RegisterTokens;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class CreateRegisterLinkCommand extends Command {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('user:register_link')
			->setDescription('Create a register link');
	}

	/**
	 * @var RegisterTokens
	 */
	private $_register_tokens;

	/**
	 * @Inject("@RegisterTokens")
	 * @param RegisterTokens $register_tokens
	 */
	public function __construct(RegisterTokens $register_tokens) {
		$this->_register_tokens = $register_tokens;

		parent::__construct();
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$token = $this->_register_tokens->addToken();

		$link = sprintf('/register/?token=%s', $token);

		$output->writeln(sprintf('<info>%s</info>', $link));
	}

} 
