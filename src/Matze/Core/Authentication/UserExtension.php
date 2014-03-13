<?php

namespace Matze\Core\Authentication;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig_Extension;

/**
 * @TwigExtension
 */
class UserExtension extends Twig_Extension {
	/**
	 * @var Session
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
			'hasRole' => new \Twig_Function_Method($this, 'hasRole'),
			'getFlashBag' => new \Twig_Function_Method($this, 'getFlashBag')
		];
	}

	/**
	 * @return UserVO
	 */
	public function getCurrentUser() {
		$user = $this->_session->get('user');

		return $user ?: new AnonymusUserVO();
	}

	/**
	 * @return UserVO
	 */
	public function isLoggedIn() {
		/** @var UserVO|null $user */
		$user = $this->_session->get('user');

		if (empty($user)) {
			return false;
		}

		return (bool)$user->id;
	}

	/**
	 * @return array[]
	 */
	public function getFlashBag() {
		return $this->_session->getFlashBag()->all();
	}

	/**
	 * @param string $role
	 * @return boolean
	 */
	public function hasRole($role) {
		/** @var UserVO $user */
		$user = $this->_session->get('user');

		if (empty($user)) {
			return false;
		}

		return $user->hasRole($role);
	}

}