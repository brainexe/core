<?php

namespace Matze\Core\Authentication;

use Matze\Core\Application\UserException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;

/**
 * @Service
 */
class Register {

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
	 * @param User $user
	 * @param Session|\Symfony\Component\HttpFoundation\Session\SessionInterface $session
	 * @throws UserException
	 * @return integer
	 */
	public function register(User $user, Session $session) {
		try {
			$this->_user_provider->loadUserByUsername($user->getUsername());

			throw new UserException(sprintf("User %s already exists", $user->getUsername()));
		} catch (UsernameNotFoundException $e) {
			// all fine
		}

		$user_id = $this->_user_provider->register($user);

		$session->set('user', $user);

		return $user_id;
	}

}