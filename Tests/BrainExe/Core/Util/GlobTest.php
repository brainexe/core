<?php

namespace Tests\BrainExe\Core\Util;

use BrainExe\Core\Util\Glob;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers \BrainExe\Core\Util\Glob
 */
class GlobTest extends TestCase
{

    /**
     * @var Glob
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Glob();
    }

    public function testExecGlob()
    {
        $pattern = '*';
        $actual  = $this->subject->execGlob($pattern);
        $this->assertInternalType('array', $actual);
        $this->assertNotEmpty($actual);
    }

    public function testEmpty()
    {
        $pattern = 'notexistingfooobar';
        $actual  = $this->subject->execGlob($pattern);
        $this->assertEquals([], $actual);
    }
}
