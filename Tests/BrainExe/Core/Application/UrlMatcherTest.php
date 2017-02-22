<?php

namespace Tests\BrainExe\Core\Application;

use BrainExe\Core\Application\UrlMatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \BrainExe\Core\Application\UrlMatcher
 */
class UrlMatcherTest extends TestCase
{

    /**
     * @var UrlMatcher
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new UrlMatcher();
    }

    public function testIndexWithoutPathInfo()
    {
        $request = new Request();

        $actual = $this->subject->match($request);

        $this->assertEquals('index', $actual['_route']);
    }

    public function testIndex()
    {
        $request = new Request();
        $request->server->set('REQUEST_URI', '/');

        $actual = $this->subject->match($request);

        $this->assertEquals('index', $actual['_route']);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testNotExisting()
    {
        $request = new Request();
        $request->server->set('REQUEST_URI', '/notexistingloremipsum');

        $this->subject->match($request);
    }
}
