<?php

namespace Tests\BrainExe\Core\Middleware\CacheMiddleware;

use BrainExe\Core\Application\Cache\RedisCache;
use BrainExe\Core\Middleware\CacheMiddleware;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject;
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
		$this->_mockLogger = $this->getMock(Logger::class, [], [], '', false);
		$this->_subject = new CacheMiddleware(true);
		$this->_subject->setCache($this->_mockRedisCache);
		$this->_subject->setLogger($this->_mockLogger);
	}

	public function testProcessRequest() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$route = new Route();
		$route_name = null;
		$this->_subject->processRequest($request, $route, $route_name);
	}

	public function testProcessResponse() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$response = new Response();
		$this->_subject->processResponse($request, $response);
	}

}
