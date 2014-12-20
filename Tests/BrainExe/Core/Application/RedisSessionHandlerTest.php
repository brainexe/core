<?php

namespace BrainExe\Tests\Core\Application;

use BrainExe\Core\Application\RedisSessionHandler;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use BrainExe\Core\Redis\Redis;

class RedisSessionHandlerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RedisSessionHandler
     */
    private $subject;

    /**
     * @var Redis|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);

        $this->subject = new RedisSessionHandler();
        $this->subject->setRedis($this->mockRedis);
    }

    public function testReadSession()
    {
        $payload    = 'foobar';
        $session_id = '121212';

        $this->mockRedis
        ->expects($this->once())
        ->method('get')
        ->with("session:$session_id")
        ->will($this->returnValue($payload));

        $actualResult = $this->subject->read($session_id);

        $this->assertEquals($payload, $actualResult);
    }

    public function testWriteSession()
    {
        $payload    = 'foobar';
        $session_id = '121212';

        $this->subject->open(null, $session_id);

        $this->mockRedis
        ->expects($this->once())
        ->method('setex')
        ->with("session:$session_id", $this->isType('integer'), $payload);

        $this->subject->write($session_id, $payload);
    }

    public function testDestroySession()
    {
        $session_id = '121212';

        $this->mockRedis
        ->expects($this->once())
        ->method('del')
        ->with("session:$session_id");

        $this->subject->destroy($session_id);
        $this->subject->close();
        $this->subject->gc(0);

    }
}
