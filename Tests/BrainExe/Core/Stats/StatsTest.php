<?php

namespace Tests\BrainExe\Core\Stats;

use BrainExe\Core\Stats\Gateway;
use BrainExe\Core\Stats\Stats;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Stats\Stats
 */
class StatsTest extends TestCase
{

    /**
     * @var Stats
     */
    private $subject;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    public function setUp()
    {
        $this->gateway = $this->getMock(Gateway::class);
        $this->subject = new Stats($this->gateway);
    }

    public function testIncrease()
    {
        $key   = 'key';
        $value = 2;

        $this->gateway
            ->expects($this->once())
            ->method('increase')
            ->with([$key => $value]);

        $this->subject->increase($key, $value);
    }

    public function testSet()
    {
        $key   = 'key';
        $value = 'value';

        $this->gateway
            ->expects($this->once())
            ->method('set')
            ->with($key, $value);

        $this->subject->set($key, $value);
    }

    public function testSetArray()
    {
        $key   = 'key';
        $value = 'value';

        $this->gateway
            ->expects($this->once())
            ->method('set')
            ->with($key, $value);

        $this->subject->set($key, $value);
    }

    public function testGetAll()
    {
        $expected = ['allValues'];

        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($expected);

        $actual = $this->subject->getAll();

        $this->assertEquals($expected, $actual);
    }

    public function testGet()
    {
        $key      = 'key';
        $expected = '12';

        $this->gateway
            ->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn($expected);

        $actual = $this->subject->get($key);
        $this->assertEquals($expected, $actual);
    }

}
