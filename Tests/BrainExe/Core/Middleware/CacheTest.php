<?php

namespace Tests\BrainExe\Core\Middleware\CacheMiddleware;

use BrainExe\Core\Middleware\Cache;
use Doctrine\Common\Cache\CacheProvider;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @covers BrainExe\Core\Middleware\Cache
 */
class CacheTest extends TestCase
{

    /**
     * @var Cache
     */
    private $subject;

    /**
     * @var CacheProvider|MockObject
     */
    private $cache;

    /**
     * @var Logger|MockObject
     */
    private $logger;

    public function setUp()
    {
        $this->cache  = $this->getMock(CacheProvider::class);
        $this->logger = $this->getMock(Logger::class, [], [], '', false);

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
        $request     = $this->getMock(Request::class);
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
            ->method('contains')
            ->with(Cache::PREFIX . $requestUri)
            ->willReturn(false);

        $actualResponse = $this->subject->processRequest($request, $route);
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
            ->with(Cache::PREFIX . $requestUri, $response, Cache::DEFAULT_TTL)
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
            ->method('contains')
            ->with(Cache::PREFIX . $requestUri)
            ->willReturn(true);

        $this->cache
            ->expects($this->once())
            ->method('fetch')
            ->with(Cache::PREFIX . $requestUri)
            ->willReturn($response);

        $actualResponse = $this->subject->processRequest($request, $route);

        $this->assertEquals($response, $actualResponse);
    }
}
