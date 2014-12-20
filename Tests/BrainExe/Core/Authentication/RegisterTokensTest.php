<?php

namespace Tests\BrainExe\Core\Authentication\RegisterTokens;

use BrainExe\Core\Authentication\RegisterTokens;
use BrainExe\Core\Util\IdGenerator;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use BrainExe\Core\Redis\Redis;

/**
 * @Covers BrainExe\Core\Authentication\RegisterTokens
 */
class RegisterTokensTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RegisterTokens
     */
    private $subject;

    /**
     * @var Redis|MockObject
     */
    private $mockRedis;

    /**
     * @var IdGenerator|MockObject
     */
    private $mockIdGenerator;


    public function setUp()
    {
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);
        $this->mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

        $this->subject = new RegisterTokens();
        $this->subject->setRedis($this->mockRedis);
        $this->subject->setIdGenerator($this->mockIdGenerator);
    }

    public function testAddToken()
    {
        $id = 11880;

        $this->mockIdGenerator
        ->expects($this->once())
        ->method('generateRandomId')
        ->will($this->returnValue($id));

        $this->mockRedis
        ->expects($this->once())
        ->method('sAdd')
        ->with(RegisterTokens::TOKEN_KEY, $id);

        $actualResult = $this->subject->addToken();

        $this->assertEquals($id, $actualResult);
    }

    public function testFetchToken()
    {
        $token = 11880;

        $this->mockRedis
        ->expects($this->once())
        ->method('sRem')
        ->with(RegisterTokens::TOKEN_KEY, $token)
        ->will($this->returnValue(true));

        $actualResult = $this->subject->fetchToken($token);

        $this->assertTrue($actualResult);
    }
}
