<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Core\Application\UserException;
use BrainExe\Core\Traits\ServiceContainerTrait;
use BrainExe\TOTP\OneTimePassword;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @todo private
 * @Service
 */
class Login {

	use ServiceContainerTrait;

	/**
	 * @var DatabaseUserProvider
	 */
	private $_user_provider;

	/**
	 * @Inject("@DatabaseUserProvider")
	 * @param DatabaseUserProvider $user_provider
	 */
	public function __construct(DatabaseUserProvider $user_provider) {
		$this->_user_provider = $user_provider;
	}

	/**
	 * @param string $username
	 * @param string $password
	 * @param string $one_time_token
	 * @param SessionInterface $session
	 * @throws UserException
	 * @return UserVO
	 */
	public function tryLogin($username, $password, $one_time_token, SessionInterface $session) {
		$user_vo = $this->_user_provider->loadUserByUsername($username);
		if (empty($user_vo)) {
			throw new UserException("Invalid Username");
		}

		if (!$this->_user_provider->verifyHash($password, $user_vo->getPassword())) {
			throw new UserException("Invalid Password");
		}

		if (!empty($user_vo->one_time_secret)) {
			/** @var OneTimePassword $one_time_password */
			$one_time_password = $this->getService('OneTimePassword');
			$one_time_password->verifyOneTimePassword($user_vo, $one_time_token);
		}

		$session->set('user_id', $user_vo->id);

		return $user_vo;
	}

}