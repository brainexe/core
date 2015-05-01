<?php

namespace Tests\BrainExe\Core\Application\AppKernel;

use BrainExe\Core\Application\AppKernel;
use BrainExe\Core\Application\ControllerResolver;
use BrainExe\Core\Application\SerializedRouteCollection;
use BrainExe\Core\Application\UrlMatcher;
use BrainExe\Core\Middleware\MiddlewareInterface;
use Exception;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @covers BrainExe\Core\Application\AppKernel
 */
class AppKernelTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var AppKernel
     */
    private $subject;

    /**
     * @var ControllerResolver|MockObject
     */
    private $mockControllerResolver;

    /**
     * @var SerializedRouteCollection|MockObject
     */
    private $mockRouteCollection;

    /**
     * @var MiddlewareInterface|MockObject
     */
    private $mockMiddleWare;

    /**
     * @var UrlMatcher|MockObject
     */
    private $mockUrlMatcher;

    public function setUp()
    {
        $this->mockControllerResolver = $this->getMock(ControllerResolver::class, [], [], '', false);
        $this->mockRouteCollection    = $this->getMock(SerializedRouteCollection::class, [], [], '', false);
        $this->mockMiddleWare         = $this->getMock(MiddlewareInterface::class, [], [], '', false);
        $this->mockUrlMatcher         = $this->getMock(UrlMatcher::class);

        $this->subject = new AppKernel(
            $this->mockControllerResolver,
            $this->mockRouteCollection,
            $this->mockUrlMatcher
        );

        $this->subject->setMiddlewares([$this->mockMiddleWare]);
    }

    public function testHandle()
    {
        $request  = new Request();

        $expectedResponse = new JsonResponse(['arguments']);

        $attributes = [
            '_route' => $routeName = 'route_name'
        ];

        $route = $this->getMock(Route::class, [], [], '', false);

        $this->mockMiddleWare
            ->expects($this->once())
            ->method('processResponse')
            ->with($request, $expectedResponse)
            ->willReturn($expectedResponse);

        $this->mockRouteCollection
            ->expects($this->once())
            ->method('get')
            ->with($routeName)
            ->willReturn($route);

        $this->mockMiddleWare
            ->expects($this->once())
            ->method('processRequest')
            ->with($request, $route, $routeName)
            ->willReturn(null);

        $callable = function($arguments) {
            return $arguments;
        };

        $this->mockControllerResolver
            ->expects($this->once())
            ->method('getController')
            ->with($request)
            ->willReturn($callable);

        $this->mockControllerResolver
            ->expects($this->once())
            ->method('getArguments')
            ->with($request, $callable)
            ->willReturn([['arguments']]);

        $this->mockUrlMatcher
            ->expects($this->once())
            ->method('match')
            ->with($request)
            ->willReturn($attributes);

        $type  = 1;
        $catch = 1;
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

        $route = $this->getMock(Route::class, [], [], '', false);

        $this->mockMiddleWare
            ->expects($this->once())
            ->method('processResponse')
            ->with($request, $response);

        $this->mockRouteCollection
            ->expects($this->once())
            ->method('get')
            ->with($routeName)
            ->willReturn($route);

        $this->mockMiddleWare
            ->expects($this->once())
            ->method('processRequest')
            ->with($request, $route, $routeName)
            ->willReturn($response);

        $this->mockUrlMatcher
            ->expects($this->once())
            ->method('match')
            ->with($request)
            ->willReturn($attributes);

        $type  = 1;
        $catch = 1;
        $actualResult = $this->subject->handle($request, $type, $catch);

        $this->assertEquals($response, $actualResult);
    }

    public function testHandleRequestException()
    {
        $request  = new Request();
        $response = new Response();
        $response->setContent('content');

        $exception = new Exception('exception');

        $this->mockUrlMatcher
            ->expects($this->once())
            ->method('match')
            ->willThrowException($exception);

        $this->mockMiddleWare
            ->expects($this->once())
            ->method('processException')
            ->with($request, $exception)
            ->willReturn($response);

        $this->mockMiddleWare
            ->expects($this->once())
            ->method('processResponse')
            ->with($request, $response);

        $type  = 1;
        $catch = 1;
        $actualResult = $this->subject->handle($request, $type, $catch);

        $this->assertEquals($response, $actualResult);
    }
}
