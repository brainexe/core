<?php

namespace BrainExe\Core\Util;

use BrainExe\Core\Application\UserException;

/**
 * Parse user input into an unix timestamp
 * @Service(public=false)
 */
class TimeParser {

	private $_time_modifier = [
		's' => 1,
		'm' => 60,
		'h' => 3600,
		'd' => 86400,
		'w' => 604800,
		'y' => 31536000,
	];

	/**
	 * @param string $string
	 * @throws UserException
	 * @return integer
	 */
	public function parseString($string) {
		if (empty($string)) {
			return 0;

		}
		$now = time();

		if (is_numeric($string)) {
			$timestamp = $now + (int)$string;
		} elseif (preg_match('/^(\d+)\s*(\w)$/', trim($string), $matches)) {
			$modifier = strtolower($matches[2]);
			if (empty($this->_time_modifier[$modifier])) {
				throw new UserException(sprintf('Invalid time modifier %s', $modifier));
			}

			$timestamp = $now + $matches[1] * $this->_time_modifier[$modifier];
		} else {
			$timestamp = strtotime($string);
		}

		if ($timestamp < $now) {
			throw new UserException(sprintf('Time %s is invalid', $string));
		}

		return $timestamp;
	}
} 