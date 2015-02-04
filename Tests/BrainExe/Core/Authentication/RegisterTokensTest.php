<?php

namespace Tests\BrainExe\Core\Authentication\RegisterTokens;

use BrainExe\Core\Authentication\RegisterTokens;
use BrainExe\Core\Redis\RedisInterface;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use BrainExe\Core\Redis\PhpRedis;

/**
 * @Covers BrainExe\Core\Authentication\RegisterTokens
 */
class RegisterTokensTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var RegisterTokens
     */
    private $subject;

    /**
     * @var RedisInterface|MockObject
     */
    private $mockRedis;

    /**
     * @var IdGenerator|MockObject
     */
    private $mockIdGenerator;

    public function setUp()
    {
        $this->mockRedis       = $this->getRedisMock();
        $this->mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

        $this->subject = new RegisterTokens();
        $this->subject->setRedis($this->mockRedis);
        $this->subject->setIdGenerator($this->mockIdGenerator);
    }

    public function testAddToken()
    {
        $tokenId = 11880;

        $this->mockIdGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->willReturn($tokenId);

        $this->mockRedis
            ->expects($this->once())
            ->method('sAdd')
            ->with(RegisterTokens::TOKEN_KEY, $tokenId);

        $actualResult = $this->subject->addToken();

        $this->assertEquals($tokenId, $actualResult);
    }

    public function testFetchToken()
    {
        $token = 11880;

        $this->mockRedis
            ->expects($this->once())
            ->method('sRem')
            ->with(RegisterTokens::TOKEN_KEY, $token)
            ->willReturn(true);

        $actualResult = $this->subject->fetchToken($token);

        $this->assertTrue($actualResult);
    }
}
