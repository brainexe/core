<?php

namespace Tests\BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\Controller\PasswordController;
use BrainExe\Core\Authentication\UserProvider;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BrainExe\Core\Authentication\Controller\PasswordController
 */
class PasswordControllerTest extends TestCase
{

    /**
     * @var PasswordController
     */
    private $subject;

    /**
     * @var UserProvider|MockObject
     */
    private $userProvider;

    public function setUp()
    {
        $this->userProvider = $this->getMock(UserProvider::class, [], [], '', false);

        $this->subject = new PasswordController($this->userProvider);
    }

    public function testChangePassword()
    {
        $oldPassword = 'old password';
        $newPassword = 'new password';
        $user     = new UserVO();

        $request = new Request();
        $request->request->set('oldPassword', $oldPassword);
        $request->request->set('newPassword', $newPassword);
        $request->attributes->set('user', $user);

        $this->userProvider
            ->expects($this->once())
            ->method('changePassword')
            ->with($user, $newPassword);

        $actual = $this->subject->changePassword($request);
        $this->assertTrue($actual);
    }
}
