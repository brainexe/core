<?php

namespace Tests\BrainExe\Core\Util\AbstractVO;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\Core\Util\AbstractVO;

class TestVO extends AbstractVO {
	public $test_1;
	public $test_2;
}

/**
 * @Covers BrainExe\Core\Util\AbstractVO
 */
class AbstractVOTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var AbstractVO
	 */
	private $subject;

	public function setUp() {
		$this->subject = new TestVO();
	}

	public function testFillValues() {
		$values = [
			'test_1' => 1,
			'test_2' => 2,
		];
		$this->subject->fillValues($values);

		$expected_result = new TestVO();
		$expected_result->test_1 = 1;
		$expected_result->test_2 = 2;

		$this->assertEquals($expected_result, $this->subject);
	}

}
