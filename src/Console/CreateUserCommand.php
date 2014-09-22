<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\Authentication\Register;
use BrainExe\Core\Authentication\UserVO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @Command
 */
class CreateUserCommand extends Command {

	/**
	 * @var Register
	 */
	private $_register;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('user:create')
			->setDescription('Create user')
			->addArgument('username', InputArgument::REQUIRED, 'username')
			->addArgument('password', InputArgument::REQUIRED, 'PLAIN password')
			->addArgument('roles', InputArgument::OPTIONAL, 'roles (comma-separated)');
	}

	/**
	 * @Inject("@Register")
	 * @param Register $register
	 */
	public function __construct(Register $register) {
		$this->_register = $register;

		parent::__construct();
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$username = $input->getArgument('username');
		$password = $input->getArgument('password');
		$roles = explode(',', $input->getArgument('roles'));

		$user = new UserVO();
		$user->username = $username;
		$user->password = $password;
		$user->roles = $roles;

		$session = new Session(new MockArraySessionStorage());
		$user_id = $this->_register->register($user, $session, null);

		$output->writeln(sprintf("New user-id: <info>%d</info>", $user_id));
	}

} 
