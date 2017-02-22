<?php

namespace Tests\BrainExe\Core\Application;

use BrainExe\Core\Application\SessionHandler;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use BrainExe\Core\Redis\Predis;

/**
 * @covers \BrainExe\Core\Application\SessionHandler
 */
class SessionHandlerTest extends TestCase
{
    use RedisMockTrait;

    /**
     * @var SessionHandler
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();

        $this->subject = new SessionHandler($this->redis, 100);
    }

    public function testDestroy()
    {
        $sessionId = 'myRandomID';

        $this->redis
            ->expects($this->once())
            ->method('del')
            ->with("sessions:$sessionId");

        $this->subject->destroy($sessionId);
    }

    public function testReadUndefined()
    {
        $sessionId = 'myRandomID';

        $this->redis
            ->expects($this->once())
            ->method('get')
            ->with("sessions:$sessionId")
            ->willReturn(null);

        $actual = $this->subject->read($sessionId);

        $this->assertEquals('', $actual);
    }

    public function testRead()
    {
        $sessionId = 'myRandomID';

        $this->redis
            ->expects($this->once())
            ->method('get')
            ->with("sessions:$sessionId")
            ->willReturn('foo');

        $actual = $this->subject->read($sessionId);

        $this->assertEquals('foo', $actual);
    }

    public function testWrite()
    {
        $sessionId = 'myRandomID';
        $data = 'myPayload';

        $this->redis
            ->expects($this->once())
            ->method('setex')
            ->with("sessions:$sessionId", 100, $data);

        $this->subject->write($sessionId, $data);
    }
}
