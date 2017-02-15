<?php

namespace BrainExe\Tests\Core\Index;

use PHPUnit_Framework_TestCase as TestCase;
use BrainExe\Core\Index\Config;

/**
 * @covers \BrainExe\Core\Index\Config
 */
class ConfigTest extends TestCase
{

    /**
     * @var Config
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Config(['key1' => 'value1']);
    }

    public function testConfig()
    {
        $actual   = $this->subject->config();
        $expected = ['key1' => 'value1'];

        $this->assertEquals($expected, $actual);
    }
}
