<?php

namespace Tests\BrainExe\Core\Util\Time;

use BrainExe\Core\Util\Time;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Util\Time
 */
class TimeTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Time
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new Time();
	}

	public function testNow() {
		$actual_result = $this->_subject->now();

		$this->assertEquals(time(), $actual_result, "current time", 1);
	}

	public function testDate() {
		$actual_result = $this->_subject->date('y');
		$expected_result = date('y');

		$this->assertEquals($expected_result, $actual_result);
	}

	public function testMicrotime() {
		$actual_result = $this->_subject->microtime();
		$this->assertEquals(microtime(true), $actual_result, "microtime", 100);
	}

	public function testStrtotime() {
		$string = 'tomorrow';

		$actual_result = $this->_subject->strtotime($string);

		$this->assertInternalType('integer', $actual_result);

	}

}
