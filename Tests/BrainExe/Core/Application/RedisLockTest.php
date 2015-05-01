<?php

namespace Tests\BrainExe\Core\Application\RedisLock;

use BrainExe\Core\Application\RedisLock;
use BrainExe\Core\Redis\RedisInterface;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use BrainExe\Core\Redis\PhpRedis;

/**
 * @covers BrainExe\Core\Application\RedisLock
 */
class RedisLockTest extends PHPUnit_Framework_TestCase
{
    use RedisMockTrait;

    /**
     * @var RedisLock
     */
    private $subject;

    /**
     * @var RedisInterface|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockRedis = $this->getRedisMock();

        $this->subject = new RedisLock();
        $this->subject->setRedis($this->mockRedis);
    }

    public function testLockWhenNotLockedYet()
    {
        $name     = 'lock';
        $lockTime = 10;

        $this->mockRedis
            ->expects($this->once())
            ->method('EXISTS')
            ->with("lock:$name")
            ->willReturn(false);

        $this->mockRedis
            ->expects($this->once())
            ->method('SETEX')
            ->with($name, $lockTime)
            ->willReturn(true);

        $actualResult = $this->subject->lock($name, $lockTime);

        $this->assertTrue($actualResult);
    }

    public function testLockWhenLocked()
    {
        $name = 'lock';
        $lockTime = 10;

        $this->mockRedis
            ->expects($this->once())
            ->method('EXISTS')
            ->with("lock:$name")
            ->willReturn(true);

        $actualResult = $this->subject->lock($name, $lockTime);

        $this->assertFalse($actualResult);
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
