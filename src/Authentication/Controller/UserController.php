<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Controller\ControllerInterface;

/**
 * @controller
 */
class UserController implements ControllerInterface {

	/**
	 * @return UserVO
	 * @Route("/user/current/", name="authenticate.current_user")
	 */
	public function getCurrentUser() {
		return $this->getCurrentUser();
	}


} 