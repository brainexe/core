<?php

namespace Tests\BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Application\UserException;
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

    public function testGetAvatars()
    {
        $actual = $this->subject->getAvatars();
        $this->assertEquals(UserVO::AVATARS, $actual);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage Invalid avatar: new avatar
     */
    public function testSetInvalidAvatars()
    {
        $user = new UserVO();
        $user->avatar = $avatar = 'new avatar';

        $request = new Request();
        $request->attributes->set('user', $user);

        $this->subject->setAvatars($request, $avatar);
    }

    public function testSetAvatars()
    {
        $user = new UserVO();
        $user->avatar = $avatar = UserVO::AVATAR_1;

        $request = new Request();
        $request->attributes->set('user', $user);

        $actual = $this->subject->setAvatars($request, $avatar);
        $this->assertEquals($user, $actual);
    }
}
