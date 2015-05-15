<?php

namespace Tests\BrainExe\Core\Middleware\GentimeMiddleware;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Middleware\Gentime;
use BrainExe\Core\Middleware\Parameter;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @covers BrainExe\Core\Middleware\Parameter
 */
class ParameterTest extends TestCase
{

    /**
     * @var Parameter
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Parameter();
    }

    public function testProcessGetRequest()
    {
        $route   = new Route('/route/');
        $request = new Request();
        $request->setMethod('GET');

        $this->subject->processRequest($request, $route);

        $this->assertEquals([], $request->request->all());
    }

    public function testProcessEmptyPostRequest()
    {
        $route   = new Route('/route/');
        $request = new Request([], [], [], [], [], [], '');

        $request->setMethod('POST');
        $request->headers->set('content-type', 'application/json');
        $this->subject->processRequest($request, $route);

        $this->assertEquals([], $request->request->all());
    }

    public function testProcessPostRequest()
    {
        $route   = new Route('/route/');
        $request = new Request([], [], [], [], [], [], '{"foo":"bar"}');

        $request->setMethod('POST');
        $request->headers->set('content-type', 'application/json');
        $this->subject->processRequest($request, $route);

        $this->assertEquals(['foo' => 'bar'], $request->request->all());
    }
}