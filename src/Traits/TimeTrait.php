<?php

namespace BrainExe\Core\Traits;

use BrainExe\Core\Util\Time;

trait TimeTrait {

	/**
	 * @var Time
	 */
	private $_time;

	/**
	 * @Inject("@Time")
	 * @param Time $time
	 */
	public function setTime(Time $time) {
		$this->_time = $time;
	}

	/**
	 * @return Time
	 */
	public function getTime() {
		return $this->_time;
	}

	/**
	 * @return int
	 */
	public function now() {
		return $this->_time->now();
	}

}
