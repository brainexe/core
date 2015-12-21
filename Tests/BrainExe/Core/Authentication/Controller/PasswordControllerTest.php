<?php

namespace Tests\BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\Controller\PasswordController;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BrainExe\Core\Authentication\Controller\PasswordController
 */
class PasswordControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PasswordController
     */
    private $subject;

    /**
     * @var DatabaseUserProvider|MockObject
     */
    private $userProvider;

    public function setUp()
    {
        $this->userProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);

        $this->subject = new PasswordController($this->userProvider);
    }

    public function testChangePassword()
    {
        $password = 'password';
        $user     = new UserVO();

        $request = new Request();
        $request->request->set('password', $password);
        $request->attributes->set('user', $user);

        $this->userProvider
            ->expects($this->once())
            ->method('changePassword')
            ->with($user, $password);

        $actual = $this->subject->changePassword($request);
        $this->assertTrue($actual);
    }
}
