<?php

namespace BrainExe\Core\Util;

/**
 * @Service(public=false)
 */
class Time {

	/**
	 * @return integer
	 */
	public function now() {
		return time();
	}

	/**
	 * @return integer
	 */
	public function microtime() {
		return microtime(true);
	}
}
