<?php

namespace Tests\BrainExe\Core\Middleware\AuthenticationMiddleware;

use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\LoadUser;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Middleware\Authentication;
use Exception;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Route;

/**
 * @covers BrainExe\Core\Middleware\Authentication
 */
class AuthenticationTest extends TestCase
{

    /**
     * @var Authentication
     */
    private $subject;

    /**
     * @var LoadUser|MockObject
     */
    private $loadUser;

    public function setUp()
    {
        $this->loadUser = $this->getMock(LoadUser::class, [], [], '', false);

        $this->subject = new Authentication(
            false,
            $this->loadUser
        );
    }

    public function testProcessResponse()
    {
        $request  = new Request();
        $response = new Response();
        $this->subject->processResponse($request, $response);
    }

    public function testProcessRequestWhenApplicationGuestsAllowed()
    {
        $this->subject = new Authentication(
            true,
            $this->loadUser
        );

        $userId = 42;
        $user   = $this->loadUser($userId);

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');

        $actualResult = $this->subject->processRequest($request, $route);

        $this->assertNull($actualResult);
        $this->assertEquals($userId, $request->attributes->get('user_id'));
        $this->assertEquals($user, $request->attributes->get('user'));
    }

    public function testProcessRequestForGuestRoutes()
    {
        $this->subject = new Authentication(
            false,
            $this->loadUser
        );

        $userId = 42;
        $user   = $this->loadUser($userId);

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');
        $route->setDefault('guest', true);

        $actualResult = $this->subject->processRequest($request, $route);

        $this->assertNull($actualResult);
        $this->assertEquals($userId, $request->attributes->get('user_id'));
        $this->assertEquals($user, $request->attributes->get('user'));
    }

    public function testProcessRequestWhenNotLoggedIn()
    {
        $this->subject = new Authentication(
            false,
            $this->loadUser
        );

        $userId = 0;
        $user = new AnonymusUserVO();

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');

        $this->loadUser
            ->expects($this->never())
            ->method('loadUserById');

        $actualResult = $this->subject->processRequest($request, $route);

        $this->assertInstanceOf(RedirectResponse::class, $actualResult);
        $this->assertEquals($userId, $request->attributes->get('user_id'));
        $this->assertEquals($user, $request->attributes->get('user'));
    }

    public function testProcessException()
    {
        $request   = new Request();
        $exception = new Exception("exception");

        $this->subject->processException($request, $exception);
    }

    public function testProcessRequest()
    {
        $this->subject = new Authentication(
            false,
            $this->loadUser
        );

        $userId = 42;
        $user = $this->loadUser($userId);

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');

        $actualResult = $this->subject->processRequest($request, $route);

        $this->assertNull($actualResult);
        $this->assertEquals($userId, $request->attributes->get('user_id'));
        $this->assertEquals($user, $request->attributes->get('user'));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\MethodNotAllowedException
     */
    public function testProcessRequestWithoutRole()
    {
        $this->subject = new Authentication(
            false,
            $this->loadUser
        );

        $userId = 42;
        $this->loadUser($userId);

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');
        $route->setDefault('_role', 'admin');

        $this->subject->processRequest($request, $route);
    }

    /**
     * @param int $userId
     * @return UserVO
     */
    private function loadUser($userId)
    {
        $user = new UserVO();

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($user);

        return $user;
    }
}
