<?php

namespace Tests\BrainExe\Core\Stats;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Stats\Gateway;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Stats\Gateway
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
        $this->redis   = $this->getRedisMock();
        $this->subject = new Gateway();
        $this->subject->setRedis($this->redis);
    }

    public function testIncrease()
    {
        $key   = 'key';
        $value = 'value';

        $pipeline = $this->getRedisMock();
        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($pipeline);

        $pipeline
            ->expects($this->once())
            ->method('zincrby')
            ->with(Gateway::KEY, $value, $key);

        $pipeline
            ->expects($this->once())
            ->method('execute');

        $this->subject->increase([$key => $value]);
    }

    public function testSet()
    {
        $key   = 'key';
        $value = 'value';

        $pipeline = $this->getRedisMock();
        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($pipeline);

        $pipeline
            ->expects($this->once())
            ->method('hset')
            ->with(Gateway::KEY, $key, $value);

        $pipeline
            ->expects($this->once())
            ->method('execute');

        $this->subject->set($key, $value);
    }

    public function testSetEmpty()
    {
        $key   = 'key';
        $value = 0;

        $pipeline = $this->getRedisMock();
        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($pipeline);

        $pipeline
            ->expects($this->once())
            ->method('zrem')
            ->with(Gateway::KEY);

        $pipeline
            ->expects($this->once())
            ->method('execute');

        $this->subject->set($key, $value);
    }

    public function testGetAll()
    {
        $expected = ['values'];

        $this->redis
            ->expects($this->once())
            ->method('zrevrangebyscore')
            ->with(Gateway::KEY)
            ->willReturn($expected);

        $actual = $this->subject->getAll();

        $this->assertEquals($expected, $actual);
    }

    public function testGet()
    {
        $key   = 'mockKey';
        $value = 22;

        $this->redis
            ->expects($this->once())
            ->method('hget')
            ->with(Gateway::KEY, $key)
            ->willReturn($value);

        $actualResult = $this->subject->get($key);
        $this->assertEquals($value, $actualResult);
    }
}
