<?php

namespace Tests\BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\Controller\PasswordController;
use BrainExe\Core\Authentication\PasswordHasher;
use BrainExe\Core\Authentication\UserProvider;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \BrainExe\Core\Authentication\Controller\PasswordController
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

    /**
     * @var PasswordHasher|MockObject
     */
    private $passwordHasher;

    public function setUp()
    {
        $this->userProvider   = $this->createMock(UserProvider::class);
        $this->passwordHasher = $this->createMock(PasswordHasher::class);

        $this->subject = new PasswordController(
            $this->userProvider,
            $this->passwordHasher
        );
    }

    public function testChangePasswordWithValidPassword()
    {
        $oldPassword = 'old password';
        $newPassword = 'new password';

        $user = new UserVO();
        $user->password_hash = 'hash';

        $request = new Request();
        $request->request->set('oldPassword', $oldPassword);
        $request->request->set('newPassword', $newPassword);
        $request->attributes->set('user', $user);

        $this->passwordHasher
            ->expects($this->once())
            ->method('verifyHash')
            ->with($oldPassword, 'hash')
            ->willReturn(true);

        $this->userProvider
            ->expects($this->once())
            ->method('changePassword')
            ->with($user, $newPassword);

        $actual = $this->subject->changePassword($request);
        $this->assertTrue($actual);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage Invalid Password given
     */
    public function testChangePasswordWithInvalidPassword()
    {
        $oldPassword = 'old password';
        $newPassword = 'new password';

        $user = new UserVO();
        $user->password_hash = 'hash';

        $request = new Request();
        $request->request->set('oldPassword', $oldPassword);
        $request->request->set('newPassword', $newPassword);
        $request->attributes->set('user', $user);

        $this->passwordHasher
            ->expects($this->once())
            ->method('verifyHash')
            ->with($oldPassword, 'hash')
            ->willReturn(false);

        $this->userProvider
            ->expects($this->never())
            ->method('changePassword');

        $this->subject->changePassword($request);
    }
}
