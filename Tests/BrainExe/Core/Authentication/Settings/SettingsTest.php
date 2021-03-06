<?php

namespace Tests\BrainExe\Core\Authentication\Settings;

use BrainExe\Core\Authentication\Settings\Gateway;
use BrainExe\Core\Authentication\Settings\Settings;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BrainExe\Core\Authentication\Settings\Settings
 */
class SettingsTest extends TestCase
{

    /**
     * @var Settings
     */
    private $subject;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    public function setUp()
    {
        $this->gateway = $this->createMock(Gateway::class);
        $this->subject = new Settings($this->gateway);
    }

    public function testGet()
    {
        $userId   = 12;
        $setting  = 'mockSetting';
        $expected = 'expected';

        $this->gateway
            ->expects($this->once())
            ->method('get')
            ->with($userId, $setting)
            ->willReturn($expected);

        $actual = $this->subject->get($userId, $setting);

        $this->assertEquals($expected, $actual);
    }

    public function testGetGlobalFallback()
    {
        $userId   = 12;
        $setting  = 'mockSetting';
        $expected = 'expected';

        $this->gateway
            ->expects($this->at(0))
            ->method('get')
            ->with($userId, $setting)
            ->willReturn(null);

        $this->gateway
            ->expects($this->at(1))
            ->method('get')
            ->with(0, $setting)
            ->willReturn($expected);

        $actual = $this->subject->get($userId, $setting);

        $this->assertEquals($expected, $actual);
    }

    public function testGetAll()
    {
        $userId   = 12;
        $expected = ['expected'];

        $this->gateway
            ->expects($this->at(0))
            ->method('getAll')
            ->with($userId)
            ->willReturn($expected);

        $this->gateway
            ->expects($this->at(1))
            ->method('getAll')
            ->with(0)
            ->willReturn([]);

        $actual = $this->subject->getAll($userId);

        $this->assertEquals($expected, $actual);
    }

    public function testSet()
    {
        $userId  = 12;
        $setting = 'mockSetting';
        $value   = 'mockValue';

        $this->gateway
            ->expects($this->once())
            ->method('set')
            ->with($userId, $setting, $value);

        $this->subject->set($userId, $setting, $value);
    }
}
