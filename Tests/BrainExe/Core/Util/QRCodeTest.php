<?php

namespace Tests\BrainExe\Core\Util\QRCode;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\Core\Util\QRCode;

/**
 * @Covers BrainExe\Core\Util\QRCode
 */
class QRCodeTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var QRCode
	 */
	private $subject;

	public function setUp() {
		$this->subject = new QRCode();
	}

	public function testGeneratreQRLink() {
		$data = 'data';
		$size = 250;

		$actual_result = $this->subject->generatreQRLink($data, $size);

		$expected_result = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=data';

		$this->assertEquals($expected_result, $actual_result);
	}

}
