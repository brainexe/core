<?php

namespace Matze\Core\Authentication;

use Matze\Core\Application\UserException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Service
 */
class Login {

	/**
	 * @var DatabaseUserProvider
	 */
	private $_user_provider;

	/**
	 * @Inject("@DatabaseUserProvider")
	 */
	public function __construct(DatabaseUserProvider $user_provider) {
		$this->_user_provider = $user_provider;
	}

	/**
	 * @param string $username
	 * @param string $password
	 * @param SessionInterface $session
	 * @throws UserException
	 * @return boolean
	 */
	public function tryLogin($username, $password, SessionInterface $session) {
		$user = $this->_user_provider->loadUserByUsername($username);
		if (empty($user)) {
			throw new UserException("Invalid Username");
		}

		$hashed_password = $this->_user_provider->generateHash($password);
		if ($hashed_password !== $user->getPassword()) {
			throw new UserException("Invalid Password");
		}

		$session->set('user', $user);

		return true;
	}

}