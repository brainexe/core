<?php

namespace BrainExe\Core\Traits;

use BrainExe\Core\Util\IdGenerator;
use BrainExe\Core\Util\Time;

trait TimeTrait {
	/**
	 * @var Time
	 */
	private $_time;

	/**
	 * @Inject("@Time")
	 * @param Time $_time
	 */
	public function setTime(Time $_time) {
		$this->_time = $_time;
	}

	/**
	 * @return Time
	 */
	protected function getTime() {
		return $this->_time;
	}

	/**
	 * @return int
	 */
	public function now() {
		return $this->_time->now();
	}

} 