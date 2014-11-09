<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\Login;
use BrainExe\Core\Authentication\Register;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\AddFlashTrait;
use BrainExe\Core\Traits\ServiceContainerTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractAuthenticationController implements ControllerInterface {

	use AddFlashTrait;
	use ServiceContainerTrait; // TODO evil

	/**
	 * @return RedirectResponse
	 * @Route("/user/current/", name="authenticate.current_user")
	 */
	public function getCurrentUser() {
		return new JsonResponse($this->getCurrentUser());
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/login/", name="authenticate.doLogin", methods="POST")
	 */
	public function doLogin(Request $request) {
		$username = $request->request->get('username');
		$plain_password = $request->request->get('password');
		$one_time_token = $request->request->get('one_time_token');

		/** @var Login $login */
		$login = $this->getService('Login');

		$user_vo = $login->tryLogin($username, $plain_password, $one_time_token, $request->getSession());

		$response = new JsonResponse($user_vo);
		$this->_addFlash($response, self::ALERT_SUCCESS, sprintf('Welcome %s', $user_vo->username));

		return $response;
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @Route("/user/change_password/", name="user.change_password", methods="POST")
	 */
	public function changePassword(Request $request) {
		$new_password = $request->request->get('password');
		/** @var UserVO $user */
		$user = $request->attributes->get('user');

		/** @var DatabaseUserProvider $user_provider */
		$user_provider = $this->getService('DatabaseUserProvider');
		$user_provider->changePassword($user, $new_password);

		return new JsonResponse(true);
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

		$response = new JsonResponse($user_vo);
		$this->_addFlash($response, self::ALERT_SUCCESS, sprintf('Welcome %s', $user_vo->username));

		return $response;
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/logout/", name="user.logout")
	 */
	public function logout(Request $request) {
		$request->getSession()->set('user', null);

		return new JsonResponse(new AnonymusUserVO());
	}
} 