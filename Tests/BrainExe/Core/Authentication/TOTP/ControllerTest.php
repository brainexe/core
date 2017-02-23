<?php

namespace Tests\BrainExe\Core\Authentication\TOTP;

use BrainExe\Core\Authentication\TOTP\Controller;
use BrainExe\Core\Authentication\TOTP\Data;
use BrainExe\Core\Authentication\TOTP\OneTimePassword;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \BrainExe\Core\Authentication\TOTP\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var OneTimePassword|MockObject
     */
    private $otp;

    public function setUp()
    {
        $this->otp = $this->createMock(OneTimePassword::class);

        $this->subject = new Controller($this->otp);
    }

    public function testGetOneTimeSecretWithoutSecret()
    {
        $userVo = new UserVO();
        $userVo->one_time_secret = '';

        $request = new Request();
        $request->attributes->set('user', $userVo);

        $actualResult = $this->subject->getOneTimeSecret($request);

        $this->assertNull($actualResult);
    }

    public function testGetOneTimeSecretWithSecret()
    {
        $userVo = new UserVO();
        $userVo->one_time_secret = $secret = 'secret';

        $request = new Request();
        $request->attributes->set('user', $userVo);

        $data = new Data();

        $this->otp
            ->expects($this->once())
            ->method('getData')
            ->with($secret)
            ->willReturn($data);

        $actualResult = $this->subject->getOneTimeSecret($request);

        $this->assertEquals($data, $actualResult);
    }

    public function testRequestOneTimeSecretGenerateNew()
    {
        $new  = true;
        $data = new Data();

        $userVo = new UserVO();
        $userVo->one_time_secret = 'secret';

        $request = new Request();
        $request->attributes->set('user', $userVo);
        $request->request->set('new', $new);

        $this->otp
            ->expects($this->once())
            ->method('generateSecret')
            ->with($userVo)
            ->willReturn($data);

        $actualResult = $this->subject->requestOneTimeSecret($request);

        $this->assertEquals($data, $actualResult);
    }

    public function testRequestOneTimeSecret()
    {
        $new  = false;
        $data = new Data();

        $userVo = new UserVO();
        $userVo->one_time_secret = $secret = 'secret';

        $request = new Request();
        $request->attributes->set('user', $userVo);
        $request->request->set('new', $new);

        $this->otp
            ->expects($this->once())
            ->method('getData')
            ->with($secret)
            ->willReturn($data);

        $actualResult = $this->subject->requestOneTimeSecret($request);

        $this->assertEquals($data, $actualResult);
    }

    public function testDeleteOneTimeSecret()
    {
        $userVo = new UserVO();
        $userVo->one_time_secret = 'secret';

        $request = new Request();
        $request->attributes->set('user', $userVo);

        $this->otp
            ->expects($this->once())
            ->method('deleteOneTimeSecret')
            ->with($userVo);

        $actualResult = $this->subject->deleteOneTimeSecret($request);

        $this->assertTrue($actualResult);
    }

    public function testSendCodeViaMail()
    {
        $userName = 'user_name';

        $request = new Request();
        $request->request->set('user_name', $userName);

        $this->otp
            ->expects($this->once())
            ->method('sendCodeViaMail')
            ->with($userName);

        $actualResult = $this->subject->sendCodeViaMail($request);

        $this->assertTrue($actualResult);
    }
}
