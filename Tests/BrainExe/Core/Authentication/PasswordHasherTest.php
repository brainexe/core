<?php

namespace Tests\BrainExe\Core\Authentication\PasswordHasher;

use BrainExe\Core\Authentication\PasswordHasher;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Authentication\PasswordHasher
 */
class PasswordHasherTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var PasswordHasher
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new PasswordHasher();
	}

	public function testGenerateHash() {
		$password = 'password';

		$actual_result1 = $this->_subject->generateHash($password);
		$actual_result2 = $this->_subject->generateHash($password);

		$this->assertInternalType('string', $actual_result1);
		$this->assertInternalType('string', $actual_result2);

		$this->assertNotEquals($actual_result1, $actual_result2);
	}

	public function testVerifyHash() {
		$password = 'password';

		$valid_hash = '$2y$07$bSguPj.ceocK7qSeYh9kS.d1ZgwRrcsoVBl.59dcLVy7Dwd3sQ8le';
		$invalid_hash = '$2y$10$lQfIxHU96vsdfsdfdsfsfggsfs.6';

		$actual_result = $this->_subject->verifyHash($password, $valid_hash);
		$this->assertTrue($actual_result);

		$actual_result = $this->_subject->verifyHash($password, $invalid_hash);
		$this->assertFalse($actual_result);
	}

}
