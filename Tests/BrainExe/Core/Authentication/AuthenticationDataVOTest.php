<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\AuthenticationDataVO;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase as TestCase;

class AuthenticationDataVOTest extends TestCase
{

    public function testVo()
    {
        $userVo       = new UserVO();
        $password     = 'password';
        $oneTimeToken = 'token';

        $subject = new AuthenticationDataVO($userVo, $password, $oneTimeToken);
        $this->assertEquals($userVo, $subject->userVo);
        $this->assertEquals($password, $subject->password);
        $this->assertEquals($oneTimeToken, $subject->oneTimeToken);
    }
}
