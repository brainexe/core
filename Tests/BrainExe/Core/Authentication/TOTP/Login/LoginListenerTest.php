<?php

namespace Tests\BrainExe\Core\Authentication\TOTP\Login;

use BrainExe\Core\Authentication\AuthenticationDataVO;
use BrainExe\Core\Authentication\Event\AuthenticateUserEvent;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Authentication\TOTP\Login\LoginListener;
use BrainExe\Core\Authentication\TOTP\OneTimePassword;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BrainExe\Core\Authentication\TOTP\Login\LoginListener
 */
class LoginListenerTest extends TestCase
{

    /**
     * @var LoginListener
     */
    private $subject;

    /**
     * @var OneTimePassword|MockObject
     */
    private $oneTimePassword;

    public function setUp()
    {
        $this->oneTimePassword = $this->createMock(OneTimePassword::class);

        $this->subject = new LoginListener($this->oneTimePassword);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandle()
    {
        $userVo = new UserVO();
        $userVo->one_time_secret = 'secret';

        $password = 'password';
        $token    = 'token';
        $authenticationData = new AuthenticationDataVO($userVo, $password, $token);

        $event = new AuthenticateUserEvent(
            $authenticationData,
            AuthenticateUserEvent::CHECK
        );

        $this->oneTimePassword
            ->expects($this->once())
            ->method('verifyOneTimePassword')
            ->with($userVo, $token);

        $this->subject->checkLogin($event);
    }
}
