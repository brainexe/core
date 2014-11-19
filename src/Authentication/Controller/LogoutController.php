<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Controller\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class LogoutController implements ControllerInterface {

	/**
	 * @param Request $request
	 * @return AnonymusUserVO
	 * @Route("/logout/", name="user.logout")
	 */
	public function logout(Request $request) {
		$request->getSession()->set('user', null);

		return new AnonymusUserVO();
	}
} 