<?php

namespace BrainExe\Tests\Core\Util;

use BrainExe\Core\Application\UserException;
use BrainExe\Core\Util\TimeParser;
use PHPUnit_Framework_TestCase;

class TimeParserTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TimeParser
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new TimeParser();
	}

	/**
	 * @dataProvider providerTimes
	 * @param string $input_string
	 * @param integer $expected_eta
	 */
	public function testParse($input_string, $expected_eta) {
		if (false === $expected_eta) {
			$this->setExpectedException(UserException::class);
		}

		$now = time();
		$actual_seconds = $this->_subject->parseString($input_string);

		$this->assertEquals($now + $expected_eta, $actual_seconds, "time parser", 2);
	}

	/**
	 * @return array[]
	 */
	public static function providerTimes() {
		return [
			[0, -time()],
			[2, 2],
			[-1, false],
			["2", 2],
			['5s', 5],
			['10S', 10],
			['5t', false],
			['7m', 7*60],
			['now', 0]
		];
	}
} 
