<?php

namespace Tests\BrainExe\Core\Middleware;

use BrainExe\Core\Middleware\Cache;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @covers \BrainExe\Core\Middleware\Cache
 */
class CacheTest extends TestCase
{

    /**
     * @var Cache
     */
    private $subject;

    /**
     * @var AdapterInterface|MockObject
     */
    private $cache;

    /**
     * @var Logger|MockObject
     */
    private $logger;

    public function setUp()
    {
        $this->cache  = $this->createMock(AdapterInterface::class);
        $this->logger = $this->createMock(Logger::class);

        $this->subject = new Cache(true);
        $this->subject->setCache($this->cache);
        $this->subject->setLogger($this->logger);
    }

    public function testProcessRequestPostRequestShouldDoNothing()
    {
        $request = new Request();
        $request->setMethod('POST');

        $route = new Route('/path/');

        $actualResponse = $this->subject->processRequest($request, $route);
        $this->assertNull($actualResponse);

        // response should not be saved
        $response = new Response();

        $this->cache
            ->expects($this->never())
            ->method('save');

        $this->subject->processResponse($request, $response);
    }

    public function testProcessNotCachedRequest()
    {
        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag();

        $route       = new Route('/path/');
        $requestUri  = 'request';

        $route->setOption('cache', 100);

        $request
            ->expects($this->once())
            ->method('isMethod')
            ->with('GET')
            ->willReturn(true);

        $request
            ->expects($this->once())
            ->method('getRequestUri')
            ->willReturn($requestUri);

        $actualResponse = $this->subject->processRequest($request, $route);

        $cachedItem = new CacheItem();
        $cachedItem->set($actualResponse);
        $cachedItem->expiresAfter(100);

        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->with(Cache::PREFIX . $requestUri)
            ->willReturn($cachedItem);

        $this->assertNull($actualResponse);

        // invalid response
        $response = new Response();
        $response->setStatusCode(500);
        $this->subject->processResponse($request, $response);

        // save valid response
        $response = new Response();
        $this->cache
            ->expects($this->once())
            ->method('save')
            ->with($cachedItem);

        $this->subject->processResponse($request, $response);
    }

    public function testProcessCachedRequest()
    {
        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag();

        $response    = new Response();
        $response->headers->set('X-Cache', 'hit');
        $route       = new Route('/path/');
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

        $this->cache
            ->expects($this->once())
            ->method('hasItem')
            ->with(Cache::PREFIX . $requestUri)
            ->willReturn(true);

        $cachedItem = new CacheItem();
        $cachedItem->set($response);

        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->with(Cache::PREFIX . $requestUri)
            ->willReturn($cachedItem);

        $actualResponse = $this->subject->processRequest($request, $route);

        $this->assertEquals($response, $actualResponse);
    }
}
