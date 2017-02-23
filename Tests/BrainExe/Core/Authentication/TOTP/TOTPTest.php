<?php

namespace Tests\BrainExe\Core\Authentication\TOTP;

use BrainExe\Core\Authentication\TOTP\TOTP;
use BrainExe\Core\Util\Time;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BrainExe\Core\Authentication\TOTP\TOTP
 */
class TOTPTest extends TestCase
{

    /**
     * @var TOTP
     */
    private $subject;

    /**
     * @var Time
     */
    private $time;

    public function setUp()
    {
        $label    = 'label';
        $digits   = 6;
        $digest   = 'sha1';
        $interval = 10;

        $this->time = $this->createMock(Time::class);

        $this->subject = new TOTP(
            $label,
            $digits,
            $digest,
            $interval,
            $this->time
        );
    }

    public function testGenerateURI()
    {
        $secret = 'secret';

        $actualResult = $this->subject->getUri($secret);

        $expectedResult = 'otpauth://totp/label?algorithm=sha1&digits=6&period=10&secret=ONSWG4TFOQ';

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testVerifyInvalid()
    {
        $secret    = 'MMZTGNJWHFSDGY3G';
        $token     = '100000';
        $timestamp = 1000000;

        $actualResult = $this->subject->verify($secret, $token, $timestamp);

        $this->assertFalse($actualResult);
    }

    public function testVerifyValid()
    {
        $secret    = 'MMZTGNJWHFSDGY3G';
        $token     = '465198';
        $timestamp = 1414141414;

        $actualResult = $this->subject->verify($secret, $token, $timestamp);

        $this->assertTrue($actualResult);
    }
}
