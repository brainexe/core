<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\Token;
use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BrainExe\Core\Authentication\Token
 */
class TokenTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var Token
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $predis;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->predis      = $this->getRedisMock();
        $this->idGenerator = $this->createMock(IdGenerator::class);
        $this->subject     = new Token();
        $this->subject->setRedis($this->predis);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testAddToken()
    {
        $userId = 42;
        $name   = 'myName';
        $roles  = ['role1', 'role2'];
        $token  = '0815';

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->with(40)
            ->willReturn($token);

        $this->predis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturnSelf();

        $this->predis
            ->expects($this->once())
            ->method('sadd')
            ->with('tokens:user:42', $token);

        $this->predis
            ->expects($this->once())
            ->method('hset')
            ->with('tokens', $token, '{"userId":42,"roles":["role1","role2"],"name":"myName"}');

        $this->predis
            ->expects($this->once())
            ->method('execute')
            ->willReturnSelf();

        $actual = $this->subject->addToken($userId, $roles, $name);

        $this->assertEquals($token, $actual);
    }

    public function testGetToken()
    {
        $token  = '0815';
        $tokenData = [
            'userId' => 42,
            'roles' => []
        ];

        $this->predis
            ->expects($this->once())
            ->method('hget')
            ->with('tokens', $token)
            ->willReturn(json_encode($tokenData));

        $actual = $this->subject->getToken($token);

        $this->assertEquals($tokenData, $actual);
    }

    public function testGetTokensForUserWithEmpty()
    {
        $userId = 42;

        $this->predis
            ->expects($this->once())
            ->method('smembers')
            ->with('tokens:user:42')
            ->willReturn([]);

        $actual = $this->subject->getTokensForUser($userId);

        $expected = [];

        $this->assertEquals($expected, iterator_to_array($actual));
    }

    public function testGetTokensForUser()
    {
        $userId = 42;

        $this->predis
            ->expects($this->once())
            ->method('smembers')
            ->with('tokens:user:42')
            ->willReturn(['0815', '0816']);

        $this->predis
            ->expects($this->once())
            ->method('hmget')
            ->with('tokens', ['0815', '0816'])
            ->willReturn([
                '{"userId":42,"roles":["role1","role2"]}',
                '{"userId":42,"roles":[],"name":"myName"}'
            ]);

        $actual = $this->subject->getTokensForUser($userId);

        $expected = [
            '0815' => ['roles' => ['role1', 'role2'], 'userId'=> 42],
            '0816' => ['roles' => [], 'name' => 'myName', 'userId'=> 42]
        ];

        $this->assertEquals($expected, iterator_to_array($actual));
    }

    public function testHasUserForRole()
    {
        $token  = '0815';
        $role   = 'role1';

        $tokenData = [
            'userId' => 42,
            'roles' => ['role1']
        ];

        $this->predis
            ->expects($this->once())
            ->method('hget')
            ->with('tokens', $token)
            ->willReturn(json_encode($tokenData));

        $actual = $this->subject->hasUserForRole($token, $role);

        $this->assertEquals(42, $actual);
    }

    public function testHasUserForRoleWithoutRole()
    {
        $token  = '0815';
        $role   = 'role1';

        $tokenData = [
            'userId' => 42,
            'roles' => ['role2', 'role4']
        ];

        $this->predis
            ->expects($this->once())
            ->method('hget')
            ->with('tokens', $token)
            ->willReturn(json_encode($tokenData));

        $actual = $this->subject->hasUserForRole($token, $role);

        $this->assertNull($actual);
    }

    public function testHasUserForRoleWithInvalidToken()
    {
        $token  = '0815';
        $role   = 'role1';

        $this->predis
            ->expects($this->once())
            ->method('hget')
            ->with('tokens', $token)
            ->willReturn('');

        $actual = $this->subject->hasUserForRole($token, $role);

        $this->assertNull($actual);
    }

    public function testRevokeToken()
    {
        $token = '0815';

        $tokenData = [
            'userId' => 42,
            'roles' => []
        ];

        $this->predis
            ->expects($this->once())
            ->method('hget')
            ->with('tokens', $token)
            ->willReturn(json_encode($tokenData));

        $this->predis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturnSelf();

        $this->predis
            ->expects($this->once())
            ->method('srem')
            ->with('tokens:user:42', $token);

        $this->predis
            ->expects($this->once())
            ->method('hdel')
            ->with('tokens', $token);

        $this->predis
            ->expects($this->once())
            ->method('execute')
            ->willReturnSelf();

        $this->subject->revoke($token);
    }

    public function testRevokeInvalidToken()
    {
        $token = '0815';


        $this->predis
            ->expects($this->once())
            ->method('hget')
            ->with('tokens', $token)
            ->willReturn(null);

        $this->predis
            ->expects($this->never())
            ->method('pipeline');

        $this->subject->revoke($token);
    }
}
