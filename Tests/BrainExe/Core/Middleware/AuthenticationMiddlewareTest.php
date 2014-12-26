<?php

namespace Tests\BrainExe\Core\Middleware\AuthenticationMiddleware;

use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Middleware\AuthenticationMiddleware;
use Exception;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Route;

/**
 * @Covers BrainExe\Core\Middleware\AuthenticationMiddleware
 */
class AuthenticationMiddlewareTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var AuthenticationMiddleware
     */
    private $subject;

    /**
     * @var DatabaseUserProvider|MockObject
     */
    private $mockDatabaseUserProvider;

    public function setUp()
    {
        $this->mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);

        $this->subject = new AuthenticationMiddleware(false, $this->mockDatabaseUserProvider);
    }

    public function testProcessResponse()
    {
        $request = new Request();
        $response = new Response();
        $this->subject->processResponse($request, $response);
    }

    public function testProcessRequestWhenApplicationGuestsAllowed()
    {
        $this->subject = new AuthenticationMiddleware(true, $this->mockDatabaseUserProvider);

        $userId = 42;
        $user = new UserVO();

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');
        $routeName = null;

        $this->mockDatabaseUserProvider
            ->expects($this->once())
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($user);

        $actualResult = $this->subject->processRequest($request, $route, $routeName);

        $this->assertNull($actualResult);
        $this->assertEquals($userId, $request->attributes->get('user_id'));
        $this->assertEquals($user, $request->attributes->get('user'));
    }

    public function testProcessRequestForGuestRoutes()
    {
        $this->subject = new AuthenticationMiddleware(false, $this->mockDatabaseUserProvider);

        $userId = 42;
        $user = new UserVO();

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');
        $route->setDefault('guest', true);
        $routeName = 'public stuff';

        $this->mockDatabaseUserProvider
            ->expects($this->once())
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($user);

        $actualResult = $this->subject->processRequest($request, $route, $routeName);

        $this->assertNull($actualResult);
        $this->assertEquals($userId, $request->attributes->get('user_id'));
        $this->assertEquals($user, $request->attributes->get('user'));
    }

    public function testProcessRequestWhenNotLoggedIn()
    {
        $this->subject = new AuthenticationMiddleware(false, $this->mockDatabaseUserProvider);

        $userId = 0;
        $user = new AnonymusUserVO();

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');
        $routeName = 'random.route';

        $this->mockDatabaseUserProvider
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
        $this->subject = new AuthenticationMiddleware(false, $this->mockDatabaseUserProvider);

        $userId = 42;
        $user = new UserVO();

        $session = new Session(new MockArraySessionStorage());
        $session->set('user_id', $userId);

        $request = new Request();
        $request->setSession($session);

        $route = new Route('/path/');
        $routeName = 'random.route';

        $this->mockDatabaseUserProvider
            ->expects($this->once())
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($user);

        $actualResult = $this->subject->processRequest($request, $route, $routeName);

        $this->assertNull($actualResult);
        $this->assertEquals($userId, $request->attributes->get('user_id'));
        $this->assertEquals($user, $request->attributes->get('user'));
    }
}
