<?php

namespace Tests\BrainExe\Core\Util;

use BrainExe\Core\Util\FileSystem;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Util\FileSystem
 */
class FileSystemTest extends TestCase
{

    /**
     * @var FileSystem
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new FileSystem();
    }

    public function testFileGetContents()
    {
        $fileName = ROOT . '/cache/test.php';
        $content = 'content';

        // dump
        $this->subject->dumpFile($fileName, $content);
        $this->assertTrue($this->subject->exists($fileName));

        // check
        $actualResult = $this->subject->fileGetContents($fileName);
        $this->assertEquals($content, $actualResult);

        // remove
        $this->subject->remove($fileName);
        $this->assertFalse($this->subject->exists($fileName));

    }
}
