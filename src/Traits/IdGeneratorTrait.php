<?php

namespace Matze\Core\Traits;

use Matze\Core\Util\IdGenerator;

/**
 * @todo remove: include service as @Inject
 */
trait IdGeneratorTrait {
	/**
	 * @var IdGenerator
	 */
	private $_id_generator;

	/**
	 * @Inject("@IdGenerator")
	 */
	public function setIdGenerator($id_generator) {
		$this->_id_generator = $id_generator;
	}

	/**
	 * @return integer
	 */
	protected function generateRandomNumericId() {
		return $this->_id_generator->generateRandomNumericId();
	}

	/**
	 * @return string
	 */
	protected function generateRandomId() {
		return $this->_id_generator->generateRandomId();
	}
} 