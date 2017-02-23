<?php

namespace BrainExe\Core\Authentication\TOTP;

use Base32\Base32;
use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Util\Time;

/**
 * @Service
 */
class TOTP
{

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
     * @Inject({
     *     "%totp.label%",
     *     "%totp.digits%",
     *     "%totp.digest%",
     *     "%totp.interval%"
     * })
     * @param string $label
     * @param integer $digits
     * @param string $digest
     * @param integer $interval
     * @param Time $time
     */
    public function __construct(
        string $label,
        int $digits,
        string $digest,
        int $interval,
        Time $time
    ) {
        $this->label    = $label;
        $this->digits   = $digits;
        $this->digest   = $digest;
        $this->interval = $interval;
        $this->time     = $time;
    }

    /**
     * @param string $secret
     * @param int $otp
     * @param int|null $timestamp
     * @return bool
     */
    public function verify(string $secret, $otp, int $timestamp = null) : bool
    {
        if (null === $timestamp) {
            $timestamp = $this->time->now();
        }

        for ($i = 0; $i <= 4; $i++) {
            $currentOtp = (int)$this->at($timestamp, $secret);
            if ((int)$otp === $currentOtp) {
                return true;
            }

            $timestamp -= $this->interval;
        }

        return false;
    }

    /**
     * @param string $secret
     * @return int
     */
    public function current(string $secret)
    {
        return $this->at($this->time->now(), $secret);
    }

    /**
     * @param string $secret
     * @return string
     */
    public function getUri(string $secret) : string
    {
        $opt = [];
        $opt['algorithm'] = $this->digest;
        $opt['digits']    = $this->digits;
        $opt['secret']    = trim(Base32::encode($secret), '=');
        $opt['period']    = $this->interval;

        ksort($opt);

        $params = str_replace(['+', '%7E'], ['%20', '~'], http_build_query($opt));

        return "otpauth://totp/" . rawurlencode($this->label) . "?$params";
    }

    /**
     * @param int $timestamp
     * @param string $secret
     * @return int
     */
    private function at($timestamp, $secret)
    {
        return $this->generateOTP($this->timecode($timestamp), $secret);
    }

    /**
     * @param integer $input
     * @param string $secret
     * @return int
     */
    private function generateOTP($input, $secret)
    {
        $hash = hash_hmac($this->digest, $this->intToBytestring($input), $secret);
        $hmac = [];

        foreach (str_split($hash, 2) as $hex) {
            $hmac[] = hexdec($hex);
        }

        $offset = $hmac[19] & 0xf;
        $code = ($hmac[$offset + 0] & 0x7F) << 24 |
                ($hmac[$offset + 1] & 0xFF) << 16 |
                ($hmac[$offset + 2] & 0xFF) << 8 |
                ($hmac[$offset + 3] & 0xFF);

        return $code % pow(10, $this->digits);
    }

    /**
     * @param int $timestamp
     * @return int
     */
    private function timecode($timestamp)
    {
        return (int)((((int)$timestamp * 1000) / ($this->interval * 1000)));
    }

    /**
     * @param int $int
     * @return string
     */
    private function intToBytestring($int)
    {
        $result = [];
        while ($int != 0) {
            $result[] = chr($int & 0xFF);
            $int >>= 8;
        }

        return str_pad(implode(array_reverse($result)), 8, "\000", STR_PAD_LEFT);
    }
}
