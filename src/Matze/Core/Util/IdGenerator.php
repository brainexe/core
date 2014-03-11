<?php

namespace Matze\Core\Util;

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
	 * @return string
	 */
	public function generateRandomId() {
		$id = md5(microtime() . mt_rand());

		return substr(base_convert($id, 16, 36), 0, self::ID_LENGTH);
	}
}
