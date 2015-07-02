<?php

namespace Tests\BrainExe\Core\Util\QRCode;

use BrainExe\Core\Util\QRCode;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Util\QRCode
 */
class QRCodeTest extends TestCase
{

    /**
     * @var QRCode
     */
    private $subject;

    public function setUp()
    {
        $baseUrl = 'https://qr.example.com/?size=%dx%d&data=%s';
        $this->subject = new QRCode($baseUrl);
    }

    public function testGenerateQRLink()
    {
        $data = 'data';
        $size = 250;

        $actual   = $this->subject->generateQRLink($data, $size);
        $expected = 'https://qr.example.com/?size=250x250&data=data';

        $this->assertEquals($expected, $actual);
    }
}
