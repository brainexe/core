<?php

namespace Matze\Core\Traits;

use PDO;

trait PDOTrait {

	/**
	 * @var PDO
	 */
	private $_pdo;

	/**
	 * @return PDO
	 */
	public function getPDO() {
		return $this->_pdo;
	}

	/**
	 * @Inject("@PDO")
	 */
	protected function setPDO(PDO $pdo) {
		$this->_pdo = $pdo;
	}

} 
