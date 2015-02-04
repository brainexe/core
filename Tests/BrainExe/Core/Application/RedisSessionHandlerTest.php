<?php

namespace BrainExe\Tests\Core\Application;

use BrainExe\Core\Application\RedisSessionHandler;
use BrainExe\Core\Redis\RedisInterface;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use BrainExe\Core\Redis\PhpRedis;

class RedisSessionHandlerTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var RedisSessionHandler
     */
    private $subject;

    /**
     * @var RedisInterface|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockRedis = $this->getRedisMock();

        $this->subject = new RedisSessionHandler();
        $this->subject->setRedis($this->mockRedis);
    }

    public function testReadSession()
    {
        $payload    = 'foobar';
        $sessionId  = '121212';

        $this->mockRedis
            ->expects($this->once())
            ->method('get')
            ->with("session:$sessionId")
            ->willReturn($payload);

        $actualResult = $this->subject->read($sessionId);

        $this->assertEquals($payload, $actualResult);
    }

    public function testWriteSession()
    {
        $payload    = 'foobar';
        $sessionId  = '121212';

        $this->subject->open(null, $sessionId);

        $this->mockRedis
            ->expects($this->once())
            ->method('setex')
            ->with("session:$sessionId", $this->isType('integer'), $payload);

        $this->subject->write($sessionId, $payload);
    }

    public function testDestroySession()
    {
        $sessionId = '121212';

        $this->mockRedis
            ->expects($this->once())
            ->method('del')
            ->with("session:$sessionId");

        $this->subject->destroy($sessionId);
        $this->subject->close();
        $this->subject->gc(0);

    }
}
