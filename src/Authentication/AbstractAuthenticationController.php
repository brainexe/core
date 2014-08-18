<?php

namespace Matze\Core\Authentication;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\ServiceContainerTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractAuthenticationController extends AbstractController {

	use ServiceContainerTrait;

	/**
	 * @param Request $request
	 * @Route("/register/", name="authenticate.register", methods="GET")
	 */
	abstract public function registerForm(Request $request);

	/**
	 * @param Request $request
	 * @Route("/login/", name="authenticate.login", methods="GET")
	 */
	abstract public function loginForm(Request $request);

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/login/", name="authenticate.doLogin", methods="POST")
	 */
	public function doLogin(Request $request) {
		$username = $request->request->get('username');
		$plain_password = $request->request->get('password');

		/** @var Login $login */
		$login = $this->getService('Login');

		$user_vo = $login->tryLogin($username, $plain_password, $request->getSession());

		$this->_addFlash($request, self::ALERT_SUCCESS, sprintf('Welcome %s', $user_vo->username));

		return new JsonResponse($user_vo);
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/register/", name="authenticate.doRegister", methods="POST")
	 */
	public function doRegister(Request $request) {
		$username = $request->request->get('username');
		$plain_password = $request->request->get('password');
		$token = $request->cookies->get('token');

		/** @var Register $register */
		$register = $this->getService('Register');

		$user_vo = new UserVO();
		$user_vo->username = $username;
		$user_vo->password = $plain_password;

		$register->register($user_vo, $request->getSession(), $token);

		$this->_addFlash($request, self::ALERT_SUCCESS, sprintf('Welcome %s', $user_vo->username));

		return new RedirectResponse('/');
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/logout/", name="authenticate.logout")
	 */
	public function logout(Request $request) {
		$request->getSession()->set('user', null);

		return new RedirectResponse('/');
	}
} 