<?php

namespace Matze\Core\Authentication\OneTimePassword;

use Base32\Base32;
use OTPHP\TOTP as TOTPLib;

/**
 * @Service(public=false)
 */
class TOTP extends TOTPLib {

	/**
	 * @var string
	 */
	private $secret;

	/**
	 * @var string
	 */
	private $label;

	/**
	 * @var integer
	 */
	private $digits;

	/**
	 * @var string
	 */
	private $digest;

	/**
	 * @var integer
	 */
	private $interval;

	/**
	 * @Inject({"%totp.label%","%totp.digits%","%totp.digest%","%totp.interval%"})
	 * @param string $label
	 * @param integer $digits
	 * @param string $digest
	 * @param integer $interval
	 */
	public function __construct($label, $digits, $digest, $interval) {
		$this->secret = '';
		$this->label = $label;
		$this->digits = $digits;
		$this->digest = $digest;
		$this->interval = $interval;
	}

	/**
	 * {@inheritdoc}
	 */
	public function verify($otp, $timestamp = null) {
		if (null === $timestamp) {
			$timestamp = time();
		}
		$otp = (int)$otp;

		for ($i = 0; $i<=4; $i++) {
			$current_otp = (int)$this->at($timestamp);
			if ($otp === $current_otp) {
				return true;
			}

			$timestamp -= $this->interval;
		}

		return false;
	}
	/**
	 * @param string $secret
	 */
	public function setSecret($secret) {
		$this->secret = $secret;
	}

	/**
	 * @return string The secret of the OTP
	 */
	public function getSecret() {
		return $this->secret;
	}

	/**
	 * @return string The label of the OTP
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @return string The issuer
	 */
	public function getIssuer() {
		return '';
	}

	/**
	 * @return boolean If true, the issuer will be added as a parameter in the provisioning URI
	 */
	public function isIssuerIncludedAsParameter() {
		return false;
	}

	/**
	 * @return integer Number of digits in the OTP
	 */
	public function getDigits() {
		return $this->digits;
	}

	/**
	 * @return string Digest algorithm used to calculate the OTP. Possible values are 'md5', 'sha1', 'sha256' and 'sha512'
	 */
	public function getDigest() {
		return $this->digest;
	}

	/**
	 * @return integer Get the interval of time for OTP generation (a non-null positive integer, in second)
	 */
	public function getInterval() {
		return $this->interval;
	}


	/**
	 * {@inheritdoc}
	 */
	protected function generateURI($type, $opt = array())  {
		$opt['algorithm'] = $this->getDigest();
		$opt['digits'] = $this->getDigits();
		$opt['secret'] = trim(Base32::encode($this->getSecret()), '=');

		ksort($opt);

		$params = str_replace(
			array('+', '%7E'),
			array('%20', '~'),
			http_build_query($opt)
		);
		return "otpauth://$type/".rawurlencode($this->getLabel())."?$params";
	}
}