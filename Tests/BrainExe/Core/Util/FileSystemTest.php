<?php

namespace Tests\BrainExe\Core\Util\FileSystem;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\Core\Util\FileSystem;

/**
 * @Covers BrainExe\Core\Util\FileSystem
 */
class FileSystemTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var FileSystem
	 */
	private $subject;

	public function setUp() {
		$this->subject = new FileSystem();
	}

	public function testFileGetContents() {
		$file_name = ROOT . '/cache/test.php';
		$content = 'content';

		// dump
		$this->subject->dumpFile($file_name, $content);
		$this->assertTrue($this->subject->exists($file_name));

		// check
		$actual_result = $this->subject->fileGetContents($file_name);
		$this->assertEquals($content, $actual_result);

		// remove
		$this->subject->remove($file_name);
		$this->assertFalse($this->subject->exists($file_name));

	}

}
