<?php

namespace Matze\Core\Console;

use Matze\Core\Authentication\Register;
use Matze\Core\Authentication\UserVO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @Command
 */
class CreateUserCommand extends Command {

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
	 * @var Register
	 */
	private $_register;

	/**
	 * @Inject("@Register")
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
		$user_id = $this->_register->register($user, $session);

		$output->writeln(sprintf("New user-id: <info>%d</info>", $user_id));
	}

} 
