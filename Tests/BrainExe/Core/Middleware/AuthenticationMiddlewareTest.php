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
class AuthenticationMiddlewareTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var AuthenticationMiddleware
	 */
	private $_subject;

	/**
	 * @var DatabaseUserProvider|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockDatabaseUserProvider;

	public function setUp() {
		$this->_mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);

		$this->_subject = new AuthenticationMiddleware(false, $this->_mockDatabaseUserProvider);
	}

	public function testProcessResponse() {
		$request = new Request();
		$response = new Response();
		$this->_subject->processResponse($request, $response);
	}

	public function testProcessRequestWhenApplicationGuestsAllowed() {
		$this->_subject = new AuthenticationMiddleware(true, $this->_mockDatabaseUserProvider);

		$user_id = 42;
		$user = new UserVO();

		$session = new Session(new MockArraySessionStorage());
		$session->set('user_id', $user_id);

		$request = new Request();
		$request->setSession($session);

		$route = new Route('/path/');
		$route_name = null;

		$this->_mockDatabaseUserProvider
			->expects($this->once())
			->method('loadUserById')
			->with($user_id)
			->will($this->returnValue($user));

		$actual_result = $this->_subject->processRequest($request, $route, $route_name);

		$this->assertNull($actual_result);
		$this->assertEquals($user_id, $request->attributes->get('user_id'));
		$this->assertEquals($user, $request->attributes->get('user'));
	}

	public function testProcessRequestForGuestRoutes() {
		$this->_subject = new AuthenticationMiddleware(false, $this->_mockDatabaseUserProvider);

		$user_id = 42;
		$user = new UserVO();

		$session = new Session(new MockArraySessionStorage());
		$session->set('user_id', $user_id);

		$request = new Request();
		$request->setSession($session);

		$route = new Route('/path/');
		$route->setDefault('guest', true);
		$route_name = 'public stuff';

		$this->_mockDatabaseUserProvider
			->expects($this->once())
			->method('loadUserById')
			->with($user_id)
			->will($this->returnValue($user));

		$actual_result = $this->_subject->processRequest($request, $route, $route_name);

		$this->assertNull($actual_result);
		$this->assertEquals($user_id, $request->attributes->get('user_id'));
		$this->assertEquals($user, $request->attributes->get('user'));
	}

	public function testProcessRequestWhenNotLoggedIn() {
		$this->_subject = new AuthenticationMiddleware(false, $this->_mockDatabaseUserProvider);

		$user_id = 0;
		$user = new AnonymusUserVO();

		$session = new Session(new MockArraySessionStorage());
		$session->set('user_id', $user_id);

		$request = new Request();
		$request->setSession($session);

		$route = new Route('/path/');
		$route_name = 'random.route';

		$this->_mockDatabaseUserProvider
			->expects($this->never())
			->method('loadUserById');

		$actual_result = $this->_subject->processRequest($request, $route, $route_name);

		$this->assertInstanceOf(RedirectResponse::class, $actual_result);
		$this->assertEquals($user_id, $request->attributes->get('user_id'));
		$this->assertEquals($user, $request->attributes->get('user'));
	}

	public function testProcessException() {
		$request   = new Request();
		$exception = new Exception("exception");

		$result = $this->_subject->processException($request, $exception);

		$this->assertNull($result);
	}

	public function testProcessRequest() {
		$this->_subject = new AuthenticationMiddleware(false, $this->_mockDatabaseUserProvider);

		$user_id = 42;
		$user = new UserVO();

		$session = new Session(new MockArraySessionStorage());
		$session->set('user_id', $user_id);

		$request = new Request();
		$request->setSession($session);

		$route = new Route('/path/');
		$route_name = 'random.route';

		$this->_mockDatabaseUserProvider
			->expects($this->once())
			->method('loadUserById')
			->with($user_id)
			->will($this->returnValue($user));

		$actual_result = $this->_subject->processRequest($request, $route, $route_name);

		$this->assertNull($actual_result);
		$this->assertEquals($user_id, $request->attributes->get('user_id'));
		$this->assertEquals($user, $request->attributes->get('user'));
	}


}
