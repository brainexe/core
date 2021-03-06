<?php

namespace Tests\BrainExe\Core\Application;

use ArrayIterator;
use BrainExe\Core\Application\AppKernel;
use BrainExe\Core\Application\ControllerResolver;
use BrainExe\Core\Application\SerializedRouteCollection;
use BrainExe\Core\Application\UrlMatcher;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Middleware\MiddlewareInterface;
use Exception;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @covers \BrainExe\Core\Application\AppKernel
 */
class AppKernelTest extends TestCase
{

    /**
     * @var AppKernel
     */
    private $subject;

    /**
     * @var ControllerResolver|MockObject
     */
    private $controllerResolver;

    /**
     * @var SerializedRouteCollection|MockObject
     */
    private $routeCollection;

    /**
     * @var MiddlewareInterface|MockObject
     */
    private $middleWare;

    /**
     * @var UrlMatcher|MockObject
     */
    private $urlMatcher;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->controllerResolver = $this->createMock(ControllerResolver::class);
        $this->routeCollection    = $this->createMock(SerializedRouteCollection::class);
        $this->middleWare         = $this->createMock(MiddlewareInterface::class);
        $this->urlMatcher         = $this->createMock(UrlMatcher::class);
        $this->dispatcher         = $this->createMock(EventDispatcher::class);

        $this->subject = new AppKernel(
            $this->controllerResolver,
            $this->routeCollection,
            $this->urlMatcher,
            $this->dispatcher,
            [$this->middleWare]
        );
    }

    public function testHandle()
    {
        $request = new Request();

        $expectedResponse = new JsonResponse(['arguments']);

        $attributes = [
            '_route' => $routeName = 'route_name'
        ];

        $route = $this->createMock(Route::class);

        $this->middleWare
            ->expects($this->once())
            ->method('processResponse')
            ->with($request, $expectedResponse)
            ->willReturn($expectedResponse);

        $this->routeCollection
            ->expects($this->once())
            ->method('get')
            ->with($routeName)
            ->willReturn($route);

        $this->middleWare
            ->expects($this->once())
            ->method('processRequest')
            ->with($request, $route)
            ->willReturn(null);

        $callable = function ($arguments) {
            return new ArrayIterator($arguments);
        };

        $this->controllerResolver
            ->expects($this->once())
            ->method('getController')
            ->with($request)
            ->willReturn($callable);

        $this->controllerResolver
            ->expects($this->once())
            ->method('getArguments')
            ->with($request, $callable)
            ->willReturn([['arguments']]);

        $this->urlMatcher
            ->expects($this->once())
            ->method('match')
            ->with($request)
            ->willReturn($attributes);

        $type  = 1;
        $catch = true;
        $actualResult = $this->subject->handle($request, $type, $catch);

        $this->assertEquals($expectedResponse->getContent(), $actualResult->getContent());
        $this->assertEquals($expectedResponse->getStatusCode(), $actualResult->getStatusCode());
    }

    public function testHandleRequestMiddleware()
    {
        $request  = new Request();
        $response = new Response();
        $response->setContent('content');

        $attributes = [
            '_route' => $routeName = 'route_name'
        ];

        $route = $this->createMock(Route::class);

        $this->middleWare
            ->expects($this->once())
            ->method('processResponse')
            ->with($request, $response);

        $this->routeCollection
            ->expects($this->once())
            ->method('get')
            ->with($routeName)
            ->willReturn($route);

        $this->middleWare
            ->expects($this->once())
            ->method('processRequest')
            ->with($request, $route)
            ->willReturn($response);

        $this->urlMatcher
            ->expects($this->once())
            ->method('match')
            ->with($request)
            ->willReturn($attributes);

        $type  = 1;
        $catch = true;
        $actualResult = $this->subject->handle($request, $type, $catch);

        $this->assertEquals($response, $actualResult);
    }

    public function testHandleRequestException()
    {
        $request  = new Request();
        $response = new Response();
        $response->setContent('content');

        $exception = new Exception('exception');

        $this->urlMatcher
            ->expects($this->once())
            ->method('match')
            ->willThrowException($exception);

        $this->middleWare
            ->expects($this->once())
            ->method('processException')
            ->with($request, $exception)
            ->willReturn($response);

        $this->middleWare
            ->expects($this->once())
            ->method('processResponse')
            ->with($request, $response);

        $type  = 1;
        $catch = true;
        $actualResult = $this->subject->handle($request, $type, $catch);

        $this->assertEquals($response, $actualResult);
    }
}
