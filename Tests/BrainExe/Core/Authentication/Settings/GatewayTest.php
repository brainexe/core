<?php

namespace Tests\BrainExe\Core\Authentication\Settings;

use BrainExe\Core\Authentication\Settings\Gateway;
use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Authentication\Settings\Gateway
 */
class GatewayTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var Gateway
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();

        $this->subject = new Gateway();
        $this->subject->setRedis($this->redis);
    }

    public function testGetAll()
    {
        $userId   = 42;
        $expected = ['mockValue'];

        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with('user:settings:42')
            ->willReturn(['"mockValue"']);

        $actual = $this->subject->getAll($userId);

        $this->assertEquals($expected, $actual);
    }

    public function testGet()
    {
        $userId   = 42;
        $setting  = 'mockSetting';
        $expected = 'mockValue';

        $this->redis
            ->expects($this->once())
            ->method('hget')
            ->with('user:settings:42', $setting)
            ->willReturn("\"$expected\"");

        $actual = $this->subject->get($userId, $setting);

        $this->assertEquals($expected, $actual);
    }

    public function testSet()
    {
        $userId  = 42;
        $setting = 'mockSetting';
        $value   = 'mockValue';

        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with('user:settings:42', $setting, '"mockValue"');

        $this->subject->set($userId, $setting, $value);
    }
}
