<?php

namespace Tests\BrainExe\Core\Util\IdGenerator;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\Core\Util\IdGenerator;

/**
 * @Covers BrainExe\Core\Util\IdGenerator
 */
class IdGeneratorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var IdGenerator
	 */
	private $_subject;

	public function setUp() {
		parent::setUp();
		$this->_subject = new IdGenerator();
	}

	public function testGenerateRandomNumericId() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->generateRandomNumericId();
	}

	public function testGenerateRandomId() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->generateRandomId($length);
	}

}
