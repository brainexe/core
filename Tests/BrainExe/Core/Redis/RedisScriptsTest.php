<?php

namespace Tests\BrainExe\Core\Redis\RedisScripts;

use BrainExe\Core\Redis\RedisScripts;
use PHPUnit_Framework_TestCase;

/**
 * @covers BrainExe\Core\Redis\RedisScripts
 */
class RedisScriptsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RedisScripts
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new RedisScripts();
    }

    public function testGetSha1()
    {
        $name = 'name';
        $sha1 = 'sha1';
        $script = 'script';
        $this->subject->registerScript($name, $sha1, $script);

        $actualResult = $this->subject->getSha1($name);
        $this->assertEquals($sha1, $actualResult);
    }

    public function testGetAllScripts()
    {
        $name = 'name';
        $sha1 = 'sha1';
        $script = 'script';
        $this->subject->registerScript($name, $sha1, $script);

        $actualResult = $this->subject->getAllScripts($name);
        $this->assertEquals([$sha1 => $script], $actualResult);
    }
}
