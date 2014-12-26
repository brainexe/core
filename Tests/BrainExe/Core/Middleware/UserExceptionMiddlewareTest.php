<?php

namespace Tests\BrainExe\Core\Middleware\UserExceptionMiddleware;

use BrainExe\Core\Application\ErrorView;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\DependencyInjection\ObjectFinder;
use BrainExe\Core\Middleware\UserExceptionMiddleware;
use Exception;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;

/**
 * @Covers BrainExe\Core\Middleware\UserExceptionMiddleware
 */
class UserExceptionMiddlewareTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var UserExceptionMiddleware
     */
    private $subject;

    /**
     * @var ObjectFinder|MockObject
     */
    private $mockObjectFinder;

    public function setUp()
    {
        $this->mockObjectFinder = $this->getMock(ObjectFinder::class, [], [], '', false);

        $this->subject = new UserExceptionMiddleware();
        $this->subject->setObjectFinder($this->mockObjectFinder);
    }

    /**
     * @dataProvider provideExceptionsForAjax
     * @param Exception $exception
     * @param int $expectedStatusCode
     */
    public function testProcessExceptionWithAjax($exception, $expectedStatusCode)
    {
        /** @var Request|MockObject $request */
        $request = $this->getMock(Request::class, ['isXmlHttpRequest']);

        $request
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->willReturn(true);

        $actualResult = $this->subject->processException($request, $exception);

        $this->assertEquals($expectedStatusCode, $actualResult->getStatusCode());
        $this->assertTrue($actualResult->headers->has('X-Flash'));
    }

    public function testProcessExceptionErrorView()
    {
        /** @var Request|MockObject $request */
        $request = $this->getMock(Request::class, ['isXmlHttpRequest']);
        /** @var ErrorView|MockObject $errorView */
        $errorView = $this->getMock(ErrorView::class, [], [], '', false);

        $exception = new ResourceNotFoundException();
        $responseString = 'response_string';

        $request
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->willReturn(false);

        $this->mockObjectFinder
            ->expects($this->once())
            ->method('getService')
            ->with('ErrorView')
            ->willReturn($errorView);

        $errorView
            ->expects($this->once())
            ->method('renderException')
            ->with($request, $this->isInstanceOf(UserException::class))
            ->willReturn($responseString);

        $actualResult = $this->subject->processException($request, $exception);

        $this->assertEquals(404, $actualResult->getStatusCode());
        $this->assertEquals($responseString, $actualResult->getContent());
    }

    public function testProcessRequest()
    {
        /** @var Route|MockObject $route */
        $route      = $this->getMock(Route::class, [], [], '', false);
        $request    = new Request();
        $routeName = 'route';

        $actualResult = $this->subject->processRequest($request, $route, $routeName);

        $this->assertNull($actualResult);
    }

    public function provideExceptionsForAjax()
    {
        return [
        [new ResourceNotFoundException(), 404],
        [new MethodNotAllowedException(['POST']), 405],
        [new Exception('test'), 500],
        ];
    }
}
