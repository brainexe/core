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
        $routeName = null;

        $actualResponse = $this->subject->processRequest($request, $route, $routeName);
        $this->assertNull($actualResponse);

        // response should not be saved
        $response = new Response();

        $this->mockRedisCache
            ->expects($this->never())
            ->method('save');

        $this->subject->processResponse($request, $response);
    }

    public function testProcessNotCachedRequest()
    {
        /** @var MockObject|Request $request */
        $request     = $this->getMock(Request::class);
        $route       = new Route('/path/');
        $routeName   = null;
        $requestUri  = 'request';

        $route->setOption('cache', true);

        $request
            ->expects($this->once())
            ->method('isMethod')
            ->with('GET')
            ->willReturn(true);

        $request
            ->expects($this->once())
            ->method('getRequestUri')
            ->willReturn($requestUri);

        $this->mockRedisCache
            ->expects($this->once())
            ->method('contains')
            ->with($requestUri)
            ->willReturn(false);

        $actualResponse = $this->subject->processRequest($request, $route, $routeName);
        $this->assertNull($actualResponse);

        // invalid response
        $response = new Response();
        $response->setStatusCode(500);
        $this->subject->processResponse($request, $response);

        // save valid response
        $response = new Response();
        $this->mockRedisCache
            ->expects($this->once())
            ->method('save')
            ->with($requestUri, $response, CacheMiddleware::DEFAULT_TTL)
            ->willReturn(false);

        $this->subject->processResponse($request, $response);
    }

    public function testProcessCachedRequest()
    {
        /** @var MockObject|Request $request */
        $request     = $this->getMock(Request::class);
        $response    = new Response();
        $response->headers->set('X-Cache', 'hit');
        $route       = new Route('/path/');
        $routeName   = null;
        $requestUri  = 'request';

        $route->setOption('cache', true);

        $request
            ->expects($this->once())
            ->method('isMethod')
            ->with('GET')
            ->willReturn(true);

        $request
            ->expects($this->once())
            ->method('getRequestUri')
            ->willReturn($requestUri);

        $this->mockRedisCache
            ->expects($this->once())
            ->method('contains')
            ->with($requestUri)
            ->willReturn(true);

        $this->mockRedisCache
            ->expects($this->once())
            ->method('fetch')
            ->with($requestUri)
            ->willReturn($response);

        $actualResponse = $this->subject->processRequest($request, $route, $routeName);

        $this->assertEquals($response, $actualResponse);
    }
}
