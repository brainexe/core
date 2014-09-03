<?php

namespace Matze\Core\Authentication\Controller;

use Matze\Core\Authentication\OneTimePassword\OneTimePassword;
use Matze\Core\Authentication\UserVO;
use Matze\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class OneTimePasswordController extends AbstractController {
	/**
	 * @var OneTimePassword
	 */
	private $_one_time_password;

	/**
	 * @Inject("@OneTimePassword")
	 * @param OneTimePassword $one_time_password
	 */
	public function __construct(OneTimePassword $one_time_password) {
		$this->_one_time_password = $one_time_password;
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @Route("/one_time_password/", name="user.get_one_time_secret")
	 */
	public function getOneTimeSecret(Request $request) {
		/** @var UserVO $user */
		$user_vo = $request->attributes->get('user');

		if ($user_vo->one_time_secret) {
			$secret_data = $this->_one_time_password->getData($user_vo->one_time_secret);
		} else {
			$secret_data = null;
		}

		return new JsonResponse($secret_data);
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @Route("/one_time_password/request/", name="user.request_one_time_secret", methods="POST")
	 */
	public function requestOneTimeSecret(Request $request) {
		/** @var UserVO $user */
		$user_vo = $request->attributes->get('user');
		$new_token = (bool)$request->attributes->get('new');


		if (!$user_vo->one_time_secret || $new_token) {
			$secret_data = $this->_one_time_password->generateSecret($user_vo);
		} else {
			$secret_data = $this->_one_time_password->getData($user_vo->one_time_secret);
		}

		return new JsonResponse($secret_data);
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @Route("/one_time_password/delete/", name="user.delete_one_time_secret", methods="POST")
	 */
	public function deleteOneTimeSecret(Request $request) {
		/** @var UserVO $user */
		$user_vo = $request->attributes->get('user');

		$this->_one_time_password->deleteOneTimeSecret($user_vo);

		return new JsonResponse(true);
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @Route("/one_time_password/mail/", name="authenticate.send_otp_mil", methods="POST")
	 */
	public function sendCodeViaMail(Request $request) {
		$user_name = $request->request->get('user_name');
		$this->_one_time_password->sendCodeViaMail($user_name);

		return new JsonResponse(true);
	}

} 