<?php

namespace Tests\BrainExe\Core\Middleware;

use BrainExe\Core\Application\Locale as LocaleModel;
use BrainExe\Core\Middleware\Locale;
use BrainExe\Core\Middleware\Security;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Route;

/**
 * @covers BrainExe\Core\Middleware\Security
 */
class SecurityTest extends TestCase
{

    /**
     * @var Security
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Security('socket.localhost:8080');
    }

    public function testProcessResponse()
    {
        $request  = new Request();
        $response = new Response();

        $request->server->set('HTTPS', 'on');

        $this->subject->processResponse($request, $response);

        $this->assertTrue($response->headers->has('Content-Security-Policy'));
        $this->assertTrue($response->headers->has('X-Frame-Options'));
        $this->assertTrue($response->headers->has('Strict-Transport-Security'));
    }

    public function testProcessAjaxResponse()
    {
        $request  = new Request();
        $response = new Response();

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $this->subject->processResponse($request, $response);

        $this->assertFalse($response->headers->has('Content-Security-Policy'));
        $this->assertFalse($response->headers->has('X-Frame-Options'));
        $this->assertFalse($response->headers->has('Strict-Transport-Security'));
    }
}