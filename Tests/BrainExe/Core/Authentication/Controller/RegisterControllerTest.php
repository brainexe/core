<?php

namespace Tests\BrainExe\Core\Authentication\Controller\RegisterController;

use BrainExe\Core\Authentication\Controller\RegisterController;
use BrainExe\Core\Authentication\Register;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @covers BrainExe\Core\Authentication\Controller\RegisterController
 */
class RegisterControllerTest extends TestCase
{

    /**
     * @var RegisterController
     */
    private $subject;

    /**
     * @var Register|MockObject
     */
    private $register;

    public function setUp()
    {
        $this->register = $this->getMock(Register::class, [], [], '', false);

        $this->subject = new RegisterController($this->register);
    }

    public function testDoRegister()
    {
        $username      = 'username';
        $plainPassword = 'plain password';
        $token         = 'token';

        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->request->set('username', $username);
        $request->request->set('password', $plainPassword);
        $request->cookies->set('token', $token);
        $request->setSession($session);

        $userVo           = new UserVO();
        $userVo->username = $username;
        $userVo->password = $plainPassword;

        $this->register
            ->expects($this->once())
            ->method('registerUser')
            ->with($userVo, $session, $token);

        $actualResult = $this->subject->register($request);

        $this->assertEquals($userVo, $actualResult);
    }
}
