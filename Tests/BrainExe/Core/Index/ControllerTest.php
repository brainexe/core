<?php

namespace BrainExe\Tests\Core\Index;

use PHPUnit_Framework_TestCase as TestCase;
use BrainExe\Core\Index\Controller;

/**
 * @covers \BrainExe\Core\Index\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Controller();
    }

    public function testIndex()
    {
        if (!is_dir(ROOT . 'web')) {
            mkdir(ROOT . 'web');
        }
        if (!is_file(ROOT . 'web/index.html')) {
            file_put_contents(ROOT . 'web/index.html', '');
        }

        $actual = $this->subject->index();

        $this->assertEquals(200, $actual->getStatusCode());
    }

    public function testRobots()
    {
        $actual = $this->subject->robotstxt();

        $this->assertEquals('text/plain', $actual->headers->get('Content-Type'));
        $this->assertEquals("User-agent: *\nDisallow: /", $actual->getContent());
    }
}
