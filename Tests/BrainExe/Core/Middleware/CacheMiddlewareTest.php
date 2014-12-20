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
class CacheMiddlewareTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var CacheMiddleware
     */
    private $subject;

    /**
     * @var RedisCache|MockObject
     */
    private $mockRedisCache;

    /**
     * @var Logger|MockObject
     */
    private $mockLogger;

    public function setUp()
    {
        $this->mockRedisCache = $this->getMock(RedisCache::class, [], [], '', false);
        $this->mockLogger     = $this->getMock(Logger::class, [], [], '', false);

        $this->subject = new CacheMiddleware(true);
        $this->subject->setCache($this->mockRedisCache);
        $this->subject->setLogger($this->mockLogger);
    }

    public function testProcessRequestPostRequestShouldDoNothing()
    {
        $request = new Request();
        $request->setMethod('POST');

        $route      = new Route('/path/');
        $route_name = null;

        $actual_response = $this->subject->processRequest($request, $route, $route_name);
        $this->assertNull($actual_response);

     // response should not be saved
        $response = new Response();

        $this->mockRedisCache
        ->expects($this->never())
        ->method('save');

        $this->subject->processResponse($request, $response);
    }

    public function testProcessNotCachedRequest()
    {
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

        $this->mockRedisCache
        ->expects($this->once())
        ->method('contains')
        ->with($request_uri)
        ->will($this->returnValue(false));

        $actual_response = $this->subject->processRequest($request, $route, $route_name);
        $this->assertNull($actual_response);

     // invalid response
        $response = new Response();
        $response->setStatusCode(500);
        $this->subject->processResponse($request, $response);

     // save valid response
        $response = new Response();
        $this->mockRedisCache
        ->expects($this->once())
        ->method('save')
        ->with($request_uri, $response, CacheMiddleware::TTL)
        ->will($this->returnValue(false));

        $this->subject->processResponse($request, $response);
    }

    public function testProcessCachedRequest()
    {
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

        $this->mockRedisCache
        ->expects($this->once())
        ->method('contains')
        ->with($request_uri)
        ->will($this->returnValue(true));

        $this->mockRedisCache
        ->expects($this->once())
        ->method('fetch')
        ->with($request_uri)
        ->will($this->returnValue($response));

        $actual_response = $this->subject->processRequest($request, $route, $route_name);

        $this->assertEquals($response, $actual_response);
    }
}
