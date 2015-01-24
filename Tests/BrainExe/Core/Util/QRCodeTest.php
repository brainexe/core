<?php

namespace Tests\BrainExe\Core\Util\QRCode;

use BrainExe\Core\Util\QRCode;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Util\QRCode
 */
class QRCodeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var QRCode
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new QRCode();
    }

    public function testGeneratreQRLink()
    {
        $data = 'data';
        $size = 250;

        $actualResult = $this->subject->generateQRLink($data, $size);

        $expectedResult = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=data';

        $this->assertEquals($expectedResult, $actualResult);
    }
}
