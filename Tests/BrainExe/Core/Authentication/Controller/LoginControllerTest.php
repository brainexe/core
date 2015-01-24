<?php

namespace Tests\BrainExe\Core\Authentication\Controller\LoginController;

use BrainExe\Core\Authentication\Controller\LoginController;
use BrainExe\Core\Authentication\Login;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @Covers BrainExe\Core\Authentication\Controller\LoginController
 */
class LoginControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var LoginController
     */
    private $subject;

    /**
     * @var Login|MockObject
     */
    private $mockLogin;

    public function setUp()
    {
        $this->mockLogin = $this->getMock(Login::class, [], [], '', false);

        $this->subject = new LoginController($this->mockLogin);
    }

    public function testDoLogin()
    {
        $username       = 'username';
        $plainPassword = 'plain password';
        $oneTimeToken = 'one time token';

        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->request->set('username', $username);
        $request->request->set('password', $plainPassword);
        $request->request->set('one_time_token', $oneTimeToken);
        $request->setSession($session);

        $userVo = new UserVO();

        $this->mockLogin
            ->expects($this->once())
            ->method('tryLogin')
            ->with($username, $plainPassword, $oneTimeToken, $session)
            ->willReturn($userVo);

        $actualResult = $this->subject->doLogin($request);

        $this->assertInstanceOf(JsonResponse::class, $actualResult);
    }
}