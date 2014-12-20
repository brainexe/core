<?php

namespace Tests\BrainExe\Core\Application\UrlMatcher;

use BrainExe\Core\Application\UrlMatcher;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers BrainExe\Core\Application\UrlMatcher
 */
class UrlMatcherTest extends PHPUnit_Framework_TestCase
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
