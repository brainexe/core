<?php

namespace Tests\BrainExe\Core\Middleware\UserExceptionMiddleware;

use BrainExe\Core\Application\UserException;
use BrainExe\Core\Middleware\CatchUserException;
use Exception;
use Monolog\Logger;
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

    /**
     * @var Logger
     */
    private $logger;

    public function setUp()
    {
        $this->logger = $this->getMock(Logger::class, [], [], '', false);

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
        $request = $this->getMock(Request::class, ['isXmlHttpRequest']);

        $actualResult = $this->subject->processException($request, $exception);

        $this->assertEquals($expectedStatusCode, $actualResult->getStatusCode());
        $this->assertTrue($actualResult->headers->has('X-Flash-Type'));
    }

    public function testProcessException()
    {
        /** @var Request|MockObject $request */
        $request = $this->getMock(Request::class, ['isXmlHttpRequest']);

        $exception = new ResourceNotFoundException();

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
            [new UserException('test'), 500],
            [new Exception('test'), 500],
        ];
    }
}
