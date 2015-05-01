<?php

namespace Tests\BrainExe\Core\Authentication\Controller\UserController;

use BrainExe\Core\Authentication\Controller\UserController;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BrainExe\Core\Authentication\Controller\UserController
 */
class UserControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var UserController
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new UserController();
    }

    public function testGetCurrentUser()
    {
        $userVo  = new UserVO();
        $request = new Request();

        $request->attributes->set('user', $userVo);

        $actualResult = $this->subject->getCurrentUser($request);

        $this->assertEquals($userVo, $actualResult);
    }
}
