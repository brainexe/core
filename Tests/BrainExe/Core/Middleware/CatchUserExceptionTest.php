<?php

namespace Tests\BrainExe\Core\Middleware;

use BrainExe\Core\Application\UserException;
use BrainExe\Core\Middleware\CatchUserException;
use Exception;
use Monolog\Logger;
use ParseError;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;
use TypeError;

/**
 * @covers BrainExe\Core\Middleware\CatchUserException
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

        $this->subject = new CatchUserException();
        $this->subject->setLogger($this->logger);
    }

    /**
     * @dataProvider provideExceptionsForAjax
     * @param Exception $exception
     * @param int $expectedStatusCode
     */
    public function testProcessExceptionWithAjax($exception, $expectedStatusCode)
    {
        /** @var Request|MockObject $request */
        $request = $this->createMock(Request::class);

        $actualResult = $this->subject->processException($request, $exception);

        $this->assertEquals($expectedStatusCode, $actualResult->getStatusCode());
        $this->assertTrue($actualResult->headers->has('X-Flash-Type'));
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

    public function provideExceptionsForAjax()
    {
        return [
            [new ResourceNotFoundException(), 404],
            [new MethodNotAllowedException(['POST']), 405],
            [new UserException('test'), 200],
            [new Exception('test'), 500],
            [new TypeError('test'), 500],
            [new ParseError('test'), 500],
        ];
    }
}
