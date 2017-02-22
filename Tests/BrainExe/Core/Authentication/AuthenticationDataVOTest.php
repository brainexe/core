<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\AuthenticationDataVO;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit\Framework\TestCase;

class AuthenticationDataVOTest extends TestCase
{

    public function testVo()
    {
        $userVo       = new UserVO();
        $password     = 'password';
        $oneTimeToken = 'token';

        $subject = new AuthenticationDataVO($userVo, $password, $oneTimeToken);

        $this->assertEquals($userVo, $subject->getUser());
        $this->assertEquals($password, $subject->getPassword());
        $this->assertEquals($oneTimeToken, $subject->getOneTimeToken());
    }
}
