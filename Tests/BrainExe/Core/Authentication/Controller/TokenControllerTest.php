<?php

namespace Tests\BrainExe\Core\Authentication\Controller;

use ArrayIterator;
use BrainExe\Core\Authentication\Controller\TokenController;
use BrainExe\Core\Authentication\Token;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BrainExe\Core\Authentication\Controller\TokenController
 */
class TokenControllerTest extends TestCase
{

    /**
     * @var TokenController
     */
    private $subject;

    /**
     * @var Token|MockObject
     */
    private $token;

    public function setUp()
    {
        $this->token   = $this->createMock(Token::class);
        $this->subject = new TokenController($this->token);
    }

    public function testGetTokens()
    {
        $userId = 42;
        $tokens = ['tokens'];

        $request = new Request();
        $request->attributes->set('user_id', $userId);

        $this->token
            ->expects($this->once())
            ->method('getTokensForUser')
            ->with($userId)
            ->willReturn(new ArrayIterator($tokens));

        $actual = $this->subject->getTokens($request);

        $this->assertEquals($tokens, $actual);
    }

    public function testAddToken()
    {
        $userId = 42;
        $token  = '0815';
        $roles  = ['roles'];

        $request = new Request();
        $request->attributes->set('user_id', $userId);
        $request->request->set('roles', $roles);

        $this->token
            ->expects($this->once())
            ->method('addToken')
            ->with($userId, $roles)
            ->willReturn($token);

        $actual = $this->subject->addToken($request);

        $this->assertEquals($token, $actual);
    }

    public function testRevoke()
    {
        $request = new Request();
        $token   = '0815';

        $this->token
            ->expects($this->once())
            ->method('revoke')
            ->with($token);

        $actual = $this->subject->revoke($request, $token);

        $this->assertTrue($actual);
    }
}
