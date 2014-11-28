<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\Login;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\AddFlashTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class LoginController implements ControllerInterface {

	use AddFlashTrait;

	/**
	 * @var Login
	 */
	private $_login;

	/**
	 * @inject("@Login")
	 * @param Login $login
	 */
	public function __construct(Login $login) {
		$this->_login = $login;
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/login/", name="authenticate.doLogin", methods="POST")
	 * @Guest
	 */
	public function doLogin(Request $request) {
		$username       = $request->request->get('username');
		$plain_password = $request->request->get('password');
		$one_time_token = $request->request->get('one_time_token');

		$user_vo = $this->_login->tryLogin($username, $plain_password, $one_time_token, $request->getSession());

		$response = new JsonResponse($user_vo);
		$this->_addFlash($response, self::ALERT_SUCCESS, sprintf('Welcome %s', $user_vo->username));

		return $response;
	}

} 