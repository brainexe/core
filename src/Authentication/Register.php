<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Core\Application\UserException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @Service(public=false)
 */
class Register {

	/**
	 * @var DatabaseUserProvider
	 */
	private $_user_provider;

	/**
	 * @var RegisterTokens
	 */
	private $_register_tokens;

	/**
	 * @var boolean
	 */
	private $_registration_enabled;

	/**
	 * @Inject({"@DatabaseUserProvider", "@RegisterTokens", "%application.registration_enabled%"})
	 * @param DatabaseUserProvider $user_provider
	 * @param RegisterTokens $register_tokens
	 * @param $registration_enabled
	 */
	public function __construct(DatabaseUserProvider $user_provider, RegisterTokens $register_tokens, $registration_enabled) {
		$this->_user_provider = $user_provider;
		$this->_register_tokens = $register_tokens;
		$this->_registration_enabled = $registration_enabled;
	}

	/**
	 * @param UserVO $user
	 * @param Session|SessionInterface $session
	 * @param string $token
	 * @throws UserException
	 * @return integer
	 */
	public function register(UserVO $user, Session $session, $token = null) {
		try {
			$this->_user_provider->loadUserByUsername($user->getUsername());

			throw new UserException(sprintf("User %s already exists", $user->getUsername()));
		} catch (UsernameNotFoundException $e) {
			// all fine
		}

		if (!$this->_registration_enabled
			&& $token !== null
			&& !$this->_register_tokens->fetchToken($token)
		) {
				throw new UserException("You have to provide a valid register token!");
		}

		$user_id = $this->_user_provider->register($user);

		$session->set('user', $user);

		return $user_id;
	}

}