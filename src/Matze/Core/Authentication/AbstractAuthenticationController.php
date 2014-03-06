<?php

namespace Matze\Core\Authentication;


use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\ServiceContainerTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\User;

abstract class AbstractAuthenticationController extends AbstractController {
	use ServiceContainerTrait;

	/**
	 * {@inheritdoc}
	 */
	public function getRoutes() {
		return [
			'authenticate.login' => [
				'pattern' => '/login/',
				'defaults' => ['_controller' => 'Authentication::loginForm']
			],
			'authenticate.register' => [
				'pattern' => '/register/',
				'defaults' => ['_controller' => 'Authentication::registerForm']
			],
			'authenticate.doLogin' => [
				'pattern' => '/login/login/',
				'defaults' => ['_controller' => 'Authentication::doLogin']
			],
			'authenticate.doRegister' => [
				'pattern' => '/register/register/',
				'defaults' => ['_controller' => 'Authentication::doRegister']
			],
			'authenticate.logout' => [
				'pattern' => '/logout/',
				'defaults' => ['_controller' => 'Authentication::logout']
			],
		];
	}

	/**
	 * @param Request $request
	 */
	abstract public function registerForm(Request $request);

	/**
	 * @param Request $request
	 */
	abstract public function loginForm(Request $request);

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function doLogin(Request $request) {
		$username = $request->request->get('username');
		$password = $request->request->get('password');

		/** @var Login $login */
		$login = $this->getService('Login');

		$login->tryLogin($username, $password, $request->getSession());

		return new RedirectResponse('/');
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function doRegister(Request $request) {
		$username = $request->request->get('username');
		$password = $request->request->get('password');

		/** @var Register $register */
		$register = $this->getService('Register');

		$user = new User($username, $password, []);

		$register->register($user, $request->getSession());

		return new RedirectResponse('/');
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function logout(Request $request) {
		$request->getSession()->set('user', null);

		return new RedirectResponse('/');
	}
} 