<?php

namespace Matze\Core\Authentication;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVO implements UserInterface {
	const ROLE_ADMIN = 'admin';
	const ROLE_USER = 'user';

	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var string
	 */
	public $username;

	/**
	 * @var string
	 */
	public $password_hash;

	/**
	 * @var string
	 */
	public $password;

	/**
	 * @var string
	 */
	public $email;

	public $roles = [];

	/**
	 * @param string $role
	 * @return boolean
	 */
	public function hasRole($role) {
		return in_array($role, $this->roles);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRoles() {
		return array_map(function($role_string) {
			return new Role($role_string);
		}, $this->roles);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPassword() {
		return $this->password_hash;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSalt() {
		return $this->username;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * {@inheritdoc}
	 */
	public function eraseCredentials() {
		$this->password = null;
	}
}