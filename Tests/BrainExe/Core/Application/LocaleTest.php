<?php

namespace Tests\BrainExe\Core\Application\Locale;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\Core\Application\Locale;

/**
 * @Covers BrainExe\Core\Application\Locale
 */
class LocaleTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Locale
	 */
	private $subject;

	public function setUp() {
		$this->subject = new Locale();
	}

	public function testGetLocales() {
		$actual_result = $this->subject->getLocales();
		$this->assertInternalType('array', $actual_result);
		$this->assertGreaterThan(0, count($actual_result));
	}

}
