<?php

namespace Tests\BrainExe\Core\Util\IdGenerator;

use BrainExe\Core\Util\IdGenerator;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Util\IdGenerator
 */
class IdGeneratorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var IdGenerator
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new IdGenerator();
	}

	public function testGenerateRandomNumericId() {
		$actual_result = $this->_subject->generateRandomNumericId();
		$actual_result2 = $this->_subject->generateRandomNumericId();

		$this->assertInternalType('integer', $actual_result);
		$this->assertGreaterThan(0, $actual_result);

		$this->assertNotEquals($actual_result, $actual_result2);
	}

	public function testGenerateRandomId() {
		$actual_result = $this->_subject->generateRandomId(10);
		$actual_result2 = $this->_subject->generateRandomId(10);

		$this->assertInternalType('string', $actual_result);
		$this->assertInternalType('string', $actual_result2);

		$this->assertEquals(10, strlen($actual_result));
		$this->assertEquals(10, strlen($actual_result2));

		$this->assertNotEquals($actual_result, $actual_result2);
	}

}
