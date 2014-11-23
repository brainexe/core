<?php

namespace BrainExe\Core\Authentication\Event;

use BrainExe\Core\Authentication\AuthenticationDataVO;
use BrainExe\Core\EventDispatcher\AbstractEvent;

class AuthenticateUserEvent extends AbstractEvent {

	const CHECK = 'authenticate.check';
	const AUTHENTICATED = 'authenticate.authenticated';
	const FAILED = 'authenticate.failed';

	/**
	 * @var AuthenticationDataVO
	 */
	private $authentication_data;

	/**
	 * @param AuthenticationDataVO $user_vo
	 * @param string $event_name
	 */
	public function __construct(AuthenticationDataVO $user_vo, $event_name) {
		parent::__construct($event_name);

		$this->authentication_data = $user_vo;
	}

	/**
	 * @return AuthenticationDataVO
	 */
	public function getAuthenticationData() {
		return $this->authentication_data;
	}
}