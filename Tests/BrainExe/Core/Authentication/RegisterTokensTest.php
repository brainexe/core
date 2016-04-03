<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\RegisterTokens;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use BrainExe\Core\Redis\Predis;

/**
 * @covers BrainExe\Core\Authentication\RegisterTokens
 */
class RegisterTokensTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var RegisterTokens
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->redis       = $this->getRedisMock();
        $this->idGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

        $this->subject = new RegisterTokens();
        $this->subject->setRedis($this->redis);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testAddToken()
    {
        $tokenId = 11880;

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->willReturn($tokenId);

        $this->redis
            ->expects($this->once())
            ->method('sadd')
            ->with(RegisterTokens::TOKEN_KEY, [$tokenId]);

        $actualResult = $this->subject->addToken();

        $this->assertEquals($tokenId, $actualResult);
    }

    public function testFetchToken()
    {
        $token = 11880;

        $this->redis
            ->expects($this->once())
            ->method('srem')
            ->with(RegisterTokens::TOKEN_KEY, $token)
            ->willReturn(true);

        $actualResult = $this->subject->fetchToken($token);

        $this->assertTrue($actualResult);
    }
}
