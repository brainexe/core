<?php

namespace Tests\BrainExe\Core\Middleware\AuthenticationMiddleware;

use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\IP;
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
     * @var DatabaseUserProvider|MockObject
     */
    private $userProvider;

    /**
     * @var IP|MockObject
     */
    private $mockIp;

    public function setUp()
    {
        $this->userProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);
        $this->mockIp       = $this->getMock(IP::class, [], [], '', false);

        $this->subject = new Authentication(
            false,
            false,
            $this->userProvider,
            $this->mockIp
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
            false,
            $this->userProvider,
            $this->mockIp
        );

        $userId = 42;
        $user   = $this->loadUser($userId);

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');
        $routeName = null;

        $actualResult = $this->subject->processRequest($request, $route, $routeName);

        $this->assertNull($actualResult);
        $this->assertEquals($userId, $request->attributes->get('user_id'));
        $this->assertEquals($user, $request->attributes->get('user'));
    }

    public function testProcessRequestForGuestRoutes()
    {
        $this->subject = new Authentication(
            false,
            false,
            $this->userProvider,
            $this->mockIp
        );

        $userId = 42;
        $user   = $this->loadUser($userId);

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');
        $route->setDefault('guest', true);
        $routeName = 'public stuff';

        $actualResult = $this->subject->processRequest($request, $route, $routeName);

        $this->assertNull($actualResult);
        $this->assertEquals($userId, $request->attributes->get('user_id'));
        $this->assertEquals($user, $request->attributes->get('user'));
    }

    public function testProcessRequestWhenNotLoggedIn()
    {
        $this->subject = new Authentication(
            false,
            false,
            $this->userProvider,
            $this->mockIp
        );

        $userId = 0;
        $user = new AnonymusUserVO();

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');
        $routeName = 'random.route';

        $this->userProvider
            ->expects($this->never())
            ->method('loadUserById');

        $actualResult = $this->subject->processRequest($request, $route, $routeName);

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
            false,
            $this->userProvider,
            $this->mockIp
        );

        $userId = 42;
        $user = $this->loadUser($userId);

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');
        $routeName = 'random.route';

        $actualResult = $this->subject->processRequest($request, $route, $routeName);

        $this->assertNull($actualResult);
        $this->assertEquals($userId, $request->attributes->get('user_id'));
        $this->assertEquals($user, $request->attributes->get('user'));
    }

    /**
     * @param int $userId
     * @return UserVO
     */
    private function loadUser($userId)
    {
        $user   = new UserVO();

        $this->userProvider
            ->expects($this->once())
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($user);

        return $user;
    }
}
