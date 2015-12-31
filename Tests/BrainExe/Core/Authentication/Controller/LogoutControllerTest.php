<?php

namespace Tests\BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\Controller\LogoutController;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @covers BrainExe\Core\Authentication\Controller\LogoutController
 */
class LogoutControllerTest extends TestCase
{

    /**
     * @var LogoutController
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new LogoutController();
    }

    public function testLogout()
    {
        $user = new UserVO();

        $session = new Session(new MockArraySessionStorage());
        $session->set('user', $user);

        $request = new Request();
        $request->setSession($session);

        $this->assertEquals($user, $session->get('user'));

        $actualResult = $this->subject->logout($request);

        $this->assertNull($session->get('user'));
        $this->assertNull($session->get('user_id'));
        $this->assertInstanceOf(AnonymusUserVO::class, $actualResult);
    }
}
