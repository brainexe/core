<?php

namespace Matze\Core\Traits;

use Matze\Core\Util\IdGenerator;

trait IdGeneratorTrait {
	/**
	 * @var IdGenerator
	 */
	private $_id_generator;

	/**
	 * @Inject("@IdGenerator")
	 * @param IdGenerator $id_generator
	 */
	public function setIdGenerator(IdGenerator $id_generator) {
		$this->_id_generator = $id_generator;
	}

	/**
	 * @return integer
	 */
	protected function generateRandomNumericId() {
		return $this->_id_generator->generateRandomNumericId();
	}

	/**
	 * @param integer $length
	 * @return string
	 */
	protected function generateRandomId($length = IdGenerator::ID_LENGTH) {
		return $this->_id_generator->generateRandomId($length);
	}
} 