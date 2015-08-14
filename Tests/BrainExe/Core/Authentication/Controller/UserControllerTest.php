<?php

namespace Tests\BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\Controller\UserController;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BrainExe\Core\Authentication\Controller\UserController
 */
class UserControllerTest extends TestCase
{

    /**
     * @var UserController
     */
    private $subject;

    /**
     * @var DatabaseUserProvider|MockObject
     */
    private $userProvider;

    public function setUp()
    {
        $this->userProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);
        $this->subject = new UserController($this->userProvider);
    }

    public function testGetCurrentUser()
    {
        $userVo  = new UserVO();
        $request = new Request();

        $request->attributes->set('user', $userVo);

        $actual = $this->subject->getCurrentUser($request);

        $this->assertEquals($userVo, $actual);
    }

    public function testGetList()
    {
        $list = ['user' => 42];
        $this->userProvider
            ->expects($this->once())
            ->method('getAllUserNames')
            ->willReturn($list);

        $actual = $this->subject->getList();
        $this->assertEquals([42 => 'user'], $actual);
    }
}
