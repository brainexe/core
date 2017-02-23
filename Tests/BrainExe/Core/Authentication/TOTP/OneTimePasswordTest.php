<?php

namespace Tests\BrainExe\Core\Authentication\TOTP;

use BrainExe\Core\Authentication\Exception\UserNotFoundException;
use BrainExe\Core\Authentication\TOTP\Data;
use BrainExe\Core\Authentication\TOTP\OneTimePassword;
use BrainExe\Core\Authentication\TOTP\TOTP;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Mail\SendMailEvent;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Authentication\UserProvider;
use BrainExe\Core\Util\IdGenerator;

/**
 * @covers \BrainExe\Core\Authentication\TOTP\OneTimePassword
 */
class OneTimePasswordTest extends TestCase
{

    /**
     * @var OneTimePassword
     */
    private $subject;

    /**
     * @var UserProvider|MockObject
     */
    private $userProvider;

    /**
     * @var TOTP|MockObject
     */
    private $totp;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->userProvider = $this->createMock(UserProvider::class);
        $this->totp         = $this->createMock(TOTP::class);
        $this->idGenerator  = $this->createMock(IdGenerator::class);
        $this->dispatcher   = $this->createMock(EventDispatcher::class);

        $this->subject = new OneTimePassword(
            $this->userProvider,
            $this->totp,
            $this->dispatcher,
            $this->idGenerator
        );
    }

    public function testGenerateSecret()
    {
        $userVo = new UserVO();
        $secret = 'secret';

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->with(16)
            ->willReturn($secret);

        $userVo->one_time_secret = $secret;
        $this->userProvider
            ->expects($this->once())
            ->method('setUserProperty')
            ->with($userVo, 'one_time_secret');

        $actualResult = $this->subject->generateSecret($userVo);

        $expectedResult = new Data();
        $expectedResult->secret = $secret;

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage No one time secret requested
     */
    public function testVerifyOneTimePasswordNoSecretRequested()
    {
        $userVo                  = new UserVO();
        $userVo->one_time_secret = null;
        $givenToken              = null;

        $this->subject->verifyOneTimePassword($userVo, $givenToken);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage No one time token given
     */
    public function testVerifyOneTimePasswordNoSecretGiven()
    {
        $userVo                  = new UserVO();
        $userVo->one_time_secret = 'secret';
        $givenToken              = null;

        $this->subject->verifyOneTimePassword($userVo, $givenToken);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage Invalid token
     */
    public function testVerifyOneTimePasswordWrongSecretGiven()
    {
        $userVo                  = new UserVO();
        $userVo->one_time_secret = 'secret';
        $givenToken              = 'invalid';

        $this->totp
            ->expects($this->once())
            ->method('verify')
            ->with($userVo->one_time_secret, $givenToken)
            ->willReturn(false);

        $this->subject->verifyOneTimePassword($userVo, $givenToken);
    }

    public function testVerifyOneTimePasswordValidSecretGiven()
    {
        $userVo                  = new UserVO();
        $userVo->one_time_secret = 'secret';
        $givenToken              = 'valid';

        $this->totp
            ->expects($this->once())
            ->method('verify')
            ->with($userVo->one_time_secret, $givenToken)
            ->willReturn(true);

        $this->subject->verifyOneTimePassword($userVo, $givenToken);
    }

    public function testDeleteOneTimeSecret()
    {
        $userVo = new UserVO();

        $this->userProvider
            ->expects($this->once())
            ->method('setUserProperty')
            ->with($userVo);

        $this->subject->deleteOneTimeSecret($userVo);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage Invalid username
     */
    public function testSendCodeViaMailWithInvalidMail()
    {
        $userName = 'invalid name';

        $this->userProvider
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($userName)
            ->willThrowException(new UserNotFoundException());

        $this->subject->sendCodeViaMail($userName);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage No email address defined for this user
     */
    public function testSendCodeViaMailWithoutMail()
    {
        $userName = 'invalid name';
        $userVo = new UserVO();
        $userVo->email = '';

        $this->userProvider
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($userName)
            ->willReturn($userVo);

        $this->subject->sendCodeViaMail($userName);
    }

    public function testSendCodeViaMailWithMail()
    {
        $code = '11880';
        $email = 'mail@example.com';

        $userName      = 'invalid name';
        $userVo        = new UserVO();
        $userVo->email = $email;
        $userVo->one_time_secret = 'secret';

        $event = new SendMailEvent($email, $code, $code);

        $this->totp
            ->expects($this->once())
            ->method('current')
            ->with($userVo->one_time_secret)
            ->willReturn($code);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->userProvider
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($userName)
            ->willReturn($userVo);

        $this->subject->sendCodeViaMail($userName);
    }
}
