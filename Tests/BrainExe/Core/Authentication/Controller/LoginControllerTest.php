<?php

namespace Tests\BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\Controller\LoginController;
use BrainExe\Core\Authentication\Login;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @covers \BrainExe\Core\Authentication\Controller\LoginController
 */
class LoginControllerTest extends TestCase
{

    /**
     * @var LoginController
     */
    private $subject;

    /**
     * @var Login|MockObject
     */
    private $login;

    public function setUp()
    {
        $this->login   = $this->createMock(Login::class);
        $this->subject = new LoginController($this->login);
    }

    public function testDoLogin()
    {
        $username      = 'username';
        $plainPassword = 'plain password';
        $oneTimeToken  = 'onetimetoken';

        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->request->set('username', $username);
        $request->request->set('password', $plainPassword);
        $request->request->set('one_time_token', $oneTimeToken);
        $request->setSession($session);

        $userVo = new UserVO();

        $this->login
            ->expects($this->once())
            ->method('tryLogin')
            ->with($username, $plainPassword, $oneTimeToken, $session)
            ->willReturn($userVo);

        $actualResult = $this->subject->login($request);

        $this->assertEquals($userVo, $actualResult);
    }

    public function testLoginWithTokenWithXHR()
    {
        $user    = new UserVO();
        $token   = 'token';
        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $this->login
            ->expects($this->once())
            ->method('loginWithToken')
            ->with($token)
            ->willReturn($user);

        $actual = $this->subject->loginWithToken($request, $token);

        $this->assertEquals($user, $actual);
    }

    public function testLoginWithTokenWithNotXHR()
    {
        $user     = new UserVO();
        $user->id = 1;
        $token    = 'token';
        $session  = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $this->login
            ->expects($this->once())
            ->method('loginWithToken')
            ->with($token)
            ->willReturn($user);

        $actual = $this->subject->loginWithToken($request, $token);

        $expected = new RedirectResponse('/');

        $this->assertEquals($expected, $actual);
    }

    public function testNeedsOneTimeToken()
    {
        $request = new Request();
        $request->query->set('username', 'username');

        $this->login
            ->expects($this->once())
            ->method('needsOneTimeToken')
            ->with('username')
            ->willReturn(true);

        $actual = $this->subject->needsOneTimeToken($request);

        $this->assertTrue($actual);
    }
}
