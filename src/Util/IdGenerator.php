<?php

namespace BrainExe\Core\Util;

/**
 * @Service(public=false)
 */
class IdGenerator {

	const ID_LENGTH = 10;

	/**
	 * @return integer
	 */
	public function generateRandomNumericId() {
		return mt_rand();
	}

	/**
	 * @param integer $length
	 * @return string
	 */
	public function generateRandomId($length = self::ID_LENGTH) {
		$id = md5(microtime() . mt_rand());

		return substr(base_convert($id, 10, 36), 0, $length);
	}
}
