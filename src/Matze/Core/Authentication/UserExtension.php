<?php

namespace Matze\Core\Authentication;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\User;
use Twig_Extension;

/**
 * @TwigExtension
 */
class UserExtension extends Twig_Extension {
	/**
	 * @var SessionInterface
	 */
	private $_session;

	/**
	 * @Inject("@RedisSession")
	 */
	public function __construct(SessionInterface $session) {
		$this->_session = $session;
	}

	/**
	 * {@inheritdoc}
	 */
	function getName() {
		return 'user';
	}

	public function getFunctions() {
		return [
			'getCurrentUser' => new \Twig_Function_Method($this, 'getCurrentUser'),
			'isLoggedIn' => new \Twig_Function_Method($this, 'isLoggedIn'),
			'hasRole' => new \Twig_Function_Method($this, 'hasRole')
		];
	}

	/**
	 * @return User
	 */
	public function getCurrentUser() {
		$user = $this->_session->get('user');

		return $user ?: new User('', '', []);
	}

	/**
	 * @return User
	 */
	public function isLoggedIn() {
		return (bool)$this->_session->get('user');
	}

	/**
	 * @param string $role
	 * @return boolean
	 */
	public function hasRole($role) {
		/** @var User $user */
		$user = $this->_session->get('user');

		if (empty($user)) {
			return false;
		}

		return in_array($role, $user->getRoles());
	}

}