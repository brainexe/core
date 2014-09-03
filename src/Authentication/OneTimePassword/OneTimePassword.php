<?php

namespace Matze\Core\Authentication\OneTimePassword;

use Exception;
use Matze\Core\Application\UserException;
use Matze\Core\Authentication\DatabaseUserProvider;
use Matze\Core\Authentication\UserVO;
use Matze\Core\Traits\IdGeneratorTrait;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @Service
 */
class OneTimePassword {

	use IdGeneratorTrait;

	/**
	 * @var DatabaseUserProvider
	 */
	private $_database_user_provider;
	/**
	 * @var TOTP
	 */
	private $_totp;

	/**
	 * @inject({"@DatabaseUserProvider", "@TOTP"})
	 * @param DatabaseUserProvider $databaseUserProvider
	 * @param TOTP $totp
	 */
	public function __construct(DatabaseUserProvider $databaseUserProvider, TOTP $totp) {
		$this->_database_user_provider = $databaseUserProvider;
		$this->_totp = $totp;
	}

	/**
	 * @param UserVO $user_vo
	 * @return array
	 * @todo return VO
	 */
	public function generateSecret(UserVO $user_vo) {
		$secret = $this->generateRandomId(16);

		$user_vo->one_time_secret = $secret;
		$this->_database_user_provider->setUserProperty($user_vo, 'one_time_secret');

		return $this->getData($secret);
	}

	/**
	 * @param $secret
	 * @return array
	 */
	public function getData($secret) {
		$this->_totp->setSecret($secret);

		$url = $this->_totp->getProvisioningUri();

		return [
			'secret' => $secret,
			'qr_link' => $this->_generatreQRLink($url),
			'url' => $url,
		];
	}

	/**
	 * @param string $data
	 * @param integer $size
	 * @return string
	 */
	private function _generatreQRLink($data, $size = 250) {
		$base_url = 'https://api.qrserver.com/v1/create-qr-code/?size=%dx%d&data=%s';

		return sprintf($base_url, $size, $size, urlencode($data));
	}

	/**
	 * @param UserVO $user_vo
	 * @param string $given_token
	 * @throws UserException
	 */
	public function verifyOneTimePassword(UserVO $user_vo, $given_token) {
		if (empty($user_vo->one_time_secret)) {
			throw new UserException("No one time secret requested");
		}

		if (empty($given_token)) {
			throw new UserException("No one time token given");
		}

		$this->_totp->setSecret($user_vo->one_time_secret);
		$verified = $this->_totp->verify($given_token);
		$this->_totp->setSecret('');

		if (!$verified) {
			throw new UserException('Invalid token');
		}
	}

	/**
	 * @param UserVO $user_vo
	 */
	public function deleteOneTimeSecret(UserVO $user_vo) {
		$user_vo->one_time_secret = null;

		$this->_database_user_provider->setUserProperty($user_vo, 'one_time_secret');
	}

	/**
	 * @param string $user_name
	 * @throws UserException
	 */
	public function sendCodeViaMail($user_name) {
		try {
			$user = $this->_database_user_provider->loadUserByUsername($user_name);

			if (empty($user->email)) {
				throw new UserException('No emil adress defined for this user');
			}
			$this->_totp->setSecret($user->one_time_secret);
			$code = $this->_totp->now();

			mail($user->email, $code, $code);

		} catch (UsernameNotFoundException $e) {
			throw new UserException('Invalid username');
		}
	}
} 