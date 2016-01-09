<?php

namespace Tests\BrainExe\Core\Application;

use BrainExe\Core\Application\UrlMatcher;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BrainExe\Core\Application\UrlMatcher
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

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testMatch()
    {
        $request = new Request();

        $this->subject->match($request);
    }
}
