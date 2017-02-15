<?php

namespace Tests\BrainExe\Core\Application;

use BrainExe\Core\Application\RedisLock;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use BrainExe\Core\Redis\Predis;

/**
 * @covers \BrainExe\Core\Application\RedisLock
 */
class RedisLockTest extends TestCase
{
    use RedisMockTrait;

    /**
     * @var RedisLock
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockRedis = $this->getRedisMock();

        $this->subject = new RedisLock();
        $this->subject->setRedis($this->mockRedis);
    }

    public function testLock()
    {
        $name = 'lock';
        $lockTime = 10;

        $this->mockRedis
            ->expects($this->once())
            ->method('set')
            ->with("lock:$name", "1", 'EX', $lockTime, 'NX');

        $this->subject->lock($name, $lockTime);
    }

    public function testUnlock()
    {
        $name = 'name';

        $this->mockRedis
            ->expects($this->once())
            ->method('del')
            ->with("lock:$name");

        $this->subject->unlock($name);
    }
}
