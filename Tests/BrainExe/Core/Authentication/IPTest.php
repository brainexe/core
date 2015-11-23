<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\IP;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BrainExe\Core\Authentication\IP
 */
class IPTest extends TestCase
{

    /**
     * @var IP
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new IP();
    }

    /**
     * @dataProvider provideIps
     * @param string $ip
     * @param bool $expected
     */
    public function testIsLocalRequest($ip, $expected)
    {
        $request = new Request();
        $request->server->set('REMOTE_ADDR', $ip);

        $actual  = $this->subject->isLocalRequest($request);
        $this->assertEquals($expected, $actual);
    }

    public function provideIps()
    {
        return [
            ['127.0.0.1', true],
            ['212.12.3.23', false],
            ['10.0.0.0', true],
        ];
    }
}
