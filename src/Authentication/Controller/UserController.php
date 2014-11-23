<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Controller\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @controller
 */
class UserController implements ControllerInterface {

	/**
	 * @param Request $request
	 * @return UserVO
	 * @Route("/user/current/", name="authenticate.current_user")
	 */
	public function getCurrentUser(Request $request) {
		return $request->attributes->get('user');
	}


} 