<?php

namespace Tests\BrainExe\Core\Middleware;

use BrainExe\Core\Authentication\LoadUser;
use BrainExe\Core\Authentication\Token;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Middleware\TokenAuthentication;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @covers \BrainExe\Core\Middleware\TokenAuthentication
 */
class TokenAuthenticationTest extends TestCase
{

    /**
     * @var TokenAuthentication
     */
    private $subject;

    /**
     * @var LoadUser|MockObject
     */
    private $loadUser;

    /**
     * @var Token|MockObject
     */
    private $token;

    public function setUp()
    {
        $this->loadUser = $this->createMock(LoadUser::class);
        $this->token    = $this->createMock(Token::class);

        $this->subject = new TokenAuthentication(
            $this->loadUser,
            $this->token
        );
    }

    public function testProcessResponse()
    {
        $request  = new Request();
        $response = new Response();

        $this->subject->processResponse($request, $response);
    }

    public function testNoToken()
    {
        $route = new Route('/path/');
        $request = new Request();

        $actual = $this->subject->processRequest($request, $route);

        $this->assertNull($actual);
    }

    public function testInvalidToken()
    {
        $route = new Route('/path/');
        $request = new Request();
        $request->query->set('accessToken', 'myToken');

        $this->token
            ->expects($this->once())
            ->method('hasUserForRole')
            ->with('myToken')
            ->willReturn(null);

        $actual = $this->subject->processRequest($request, $route);

        $this->assertNull($actual);
    }

    public function testValidToken()
    {
        $route = new Route('/path/');
        $request = new Request();
        $request->query->set('accessToken', 'myToken');

        $this->token
            ->expects($this->once())
            ->method('hasUserForRole')
            ->with('myToken')
            ->willReturn(42);

        $user = new UserVO();
        $user->id = 42;

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserById')
            ->with(42)
            ->willReturn($user);

        $actual = $this->subject->processRequest($request, $route);

        $this->assertNull($actual);

        $this->assertEquals($user, $request->attributes->get('user'));
    }
}
