<?php

namespace Tests\BrainExe\Core\Middleware\UserExceptionMiddleware;

use BrainExe\Core\Application\UserException;
use BrainExe\Core\DependencyInjection\ObjectFinder;
use BrainExe\Core\Middleware\CatchUserException;
use Exception;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;

/**
 * @covers BrainExe\Core\Middleware\CatchUserException
 */
class CatchUserExceptionTest extends TestCase
{

    /**
     * @var CatchUserException
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new CatchUserException();
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

    public function testProcessException()
    {
        /** @var Request|MockObject $request */
        $request = $this->getMock(Request::class, ['isXmlHttpRequest']);

        $exception = new ResourceNotFoundException();

        $request
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->willReturn(false);

        $actualResult = $this->subject->processException($request, $exception);

        $this->assertEquals(404, $actualResult->getStatusCode());
        $this->assertEquals('Page not found: ', $actualResult->getContent());
    }

    public function testProcessRequest()
    {
        /** @var Route|MockObject $route */
        $route      = $this->getMock(Route::class, [], [], '', false);
        $request    = new Request();

        $actualResult = $this->subject->processRequest($request, $route);

        $this->assertNull($actualResult);
    }

    public function provideExceptionsForAjax()
    {
        return [
            [new ResourceNotFoundException(), 404],
            [new MethodNotAllowedException(['POST']), 405],
            [new UserException('test'), 200],
            [new Exception('test'), 500],
        ];
    }
}
