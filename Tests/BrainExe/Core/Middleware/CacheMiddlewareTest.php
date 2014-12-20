<?php

namespace Tests\BrainExe\Core\Middleware\CacheMiddleware;

use BrainExe\Core\Application\Cache\RedisCache;
use BrainExe\Core\Middleware\CacheMiddleware;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @Covers BrainExe\Core\Middleware\CacheMiddleware
 */
class CacheMiddlewareTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var CacheMiddleware
	 */
	private $_subject;

	/**
	 * @var RedisCache|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedisCache;

	/**
	 * @var Logger|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockLogger;

	public function setUp() {
		$this->_mockRedisCache = $this->getMock(RedisCache::class, [], [], '', false);
		$this->_mockLogger     = $this->getMock(Logger::class, [], [], '', false);

		$this->_subject = new CacheMiddleware(true);
		$this->_subject->setCache($this->_mockRedisCache);
		$this->_subject->setLogger($this->_mockLogger);
	}

	public function testProcessRequestPostRequestShouldDoNothing() {
		$request = new Request();
		$request->setMethod('POST');

		$route      = new Route('/path/');
		$route_name = null;

		$actual_response = $this->_subject->processRequest($request, $route, $route_name);
		$this->assertNull($actual_response);

		// response should not be saved
		$response = new Response();

		$this->_mockRedisCache
			->expects($this->never())
			->method('save');

		$this->_subject->processResponse($request, $response);
	}

	public function testProcessNotCachedRequest() {
		/** @var PHPUnit_Framework_MockObject_MockObject|Request $request */
		$request     = $this->getMock(Request::class);
		$route       = new Route('/path/');
		$route_name  = null;
		$request_uri = 'request';

		$route->setOption('cache', true);

		$request
			->expects($this->once())
			->method('isMethod')
			->with('GET')
			->will($this->returnValue(true));

		$request
			->expects($this->once())
			->method('getRequestUri')
			->will($this->returnValue($request_uri));

		$this->_mockRedisCache
			->expects($this->once())
			->method('contains')
			->with($request_uri)
			->will($this->returnValue(false));

		$actual_response = $this->_subject->processRequest($request, $route, $route_name);
		$this->assertNull($actual_response);

		// invalid response
		$response = new Response();
		$response->setStatusCode(500);
		$this->_subject->processResponse($request, $response);

		// save valid response
		$response = new Response();
		$this->_mockRedisCache
			->expects($this->once())
			->method('save')
			->with($request_uri, $response, CacheMiddleware::TTL)
			->will($this->returnValue(false));

		$this->_subject->processResponse($request, $response);
	}

	public function testProcessCachedRequest() {
		/** @var PHPUnit_Framework_MockObject_MockObject|Request $request */
		$request     = $this->getMock(Request::class);
		$response    = new Response();
		$route       = new Route('/path/');
		$route_name  = null;
		$request_uri = 'request';

		$route->setOption('cache', true);

		$request
			->expects($this->once())
			->method('isMethod')
			->with('GET')
			->will($this->returnValue(true));

		$request
			->expects($this->once())
			->method('getRequestUri')
			->will($this->returnValue($request_uri));

		$this->_mockRedisCache
			->expects($this->once())
			->method('contains')
			->with($request_uri)
			->will($this->returnValue(true));

		$this->_mockRedisCache
			->expects($this->once())
			->method('fetch')
			->with($request_uri)
			->will($this->returnValue($response));

		$actual_response = $this->_subject->processRequest($request, $route, $route_name);

		$this->assertEquals($response, $actual_response);
	}

}
