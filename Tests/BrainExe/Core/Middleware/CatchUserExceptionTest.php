<?php

namespace Tests\BrainExe\Core\Middleware;

use BrainExe\Core\Application\UserException;
use BrainExe\Core\Middleware\CatchUserException;
use Exception;
use Monolog\Logger;
use ParseError;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;
use Throwable;
use TypeError;

/**
 * @covers \BrainExe\Core\Middleware\CatchUserException
 */
class CatchUserExceptionTest extends TestCase
{

    /**
     * @var CatchUserException
     */
    private $subject;

    /**
     * @var Logger
     */
    private $logger;

    public function setUp()
    {
        $this->logger = $this->createMock(Logger::class);

        $this->subject = new CatchUserException(
            $this->logger
        );
    }

    /**
     * @dataProvider provideExceptions
     * @param Throwable $exception
     * @param int $expectedStatusCode
     */
    public function testProcessExceptionWithAjax(Throwable $exception, int $expectedStatusCode)
    {
        /** @var Request|MockObject $request */
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('isXmlHttpRequest')
            ->willReturn(true);

        $actualResult = $this->subject->processException($request, $exception);

        $this->assertEquals($expectedStatusCode, $actualResult->getStatusCode());
        $this->assertTrue($actualResult->headers->has('X-Flash-Type'));
    }
    /**
     * @dataProvider provideExceptions
     * @param Throwable $exception
     * @param int $expectedStatusCode
     */
    public function testProcessExceptionWithoutAjax(Throwable $exception, int $expectedStatusCode, string $expectedMessage = '')
    {
        /** @var Request|MockObject $request */
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('isXmlHttpRequest')
            ->willReturn(false);

        $actualResult = $this->subject->processException($request, $exception);

        $this->assertStringStartsWith($expectedMessage, $actualResult->getContent());
        $this->assertEquals($expectedStatusCode, $actualResult->getStatusCode());
    }

    public function testProcessException()
    {
        /** @var Request|MockObject $request */
        $request = $this->createMock(Request::class);

        $exception = new ResourceNotFoundException();

        $actualResult = $this->subject->processException($request, $exception);

        $this->assertEquals(404, $actualResult->getStatusCode());
        $this->assertEquals('Page not found: ', $actualResult->getContent());
    }

    public function testProcessRequest()
    {
        /** @var Route|MockObject $route */
        $route      = $this->createMock(Route::class);
        $request    = new Request();

        $this->subject->processRequest($request, $route);
    }

    public function provideExceptions()
    {
        return [
            [new ResourceNotFoundException(), 404, 'Page not found: '],
            [new MethodNotAllowedException(['POST']), 405, 'You are not allowed to access the page'],
            [new UserException('test'), 200, 'test'],
            [new Exception('test'), 500, 'test'],
            [new TypeError('test'), 500, 'test'],
            [new ParseError('test'), 500, 'test'],
        ];
    }
}
