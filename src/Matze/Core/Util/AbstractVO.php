<?php

namespace Matze\Core\Util;

abstract class AbstractVO {

	/**
	 * @param array $values
	 */
	public function fillValues(array $values) {
		foreach ($values as $key => $value) {
			$this->$key = $value;
		}
	}
} 