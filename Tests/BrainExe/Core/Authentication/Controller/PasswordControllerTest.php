<?php

namespace Tests\BrainExe\Core\Authentication\Controller\PasswordController;

use BrainExe\Core\Authentication\Controller\PasswordController;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers BrainExe\Core\Authentication\Controller\PasswordController
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
    private $mockDatabaseUserProvider;

    public function setUp()
    {
        $this->mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);

        $this->subject = new PasswordController($this->mockDatabaseUserProvider);
    }

    public function testChangePassword()
    {
        $password = 'password';
        $user     = new UserVO();

        $request = new Request();
        $request->request->set('password', $password);
        $request->attributes->set('user', $user);

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('changePassword')
        ->with($user, $password);

        $actualResult = $this->subject->changePassword($request);
        $this->assertTrue($actualResult);
    }
}
