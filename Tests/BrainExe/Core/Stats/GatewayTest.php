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

        $this->redis
            ->expects($this->once())
            ->method('hincrby')
            ->with(Gateway::KEY, $key, $value);

        $this->subject->increase($key, $value);
    }

    public function testSet()
    {
        $key   = 'key';
        $value = 'value';

        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with(Gateway::KEY, $key, $value);

        $this->subject->set($key, $value);
    }

    public function testGetAll()
    {
        $expected = ['values'];

        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with(Gateway::KEY)
            ->willReturn($expected);

        $actual = $this->subject->getAll();

        $this->assertEquals($expected, $actual);
    }

    public function testGet()
    {
        $key   = 'mockKey';
        $value = 'mockValue';

        $this->redis
            ->expects($this->once())
            ->method('hget')
            ->with(Gateway::KEY, $key)
            ->willReturn($value);

        $actualResult = $this->subject->get($key);
        $this->assertEquals($value, $actualResult);
    }

}
