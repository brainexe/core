<?php

namespace Tests\BrainExe\Core\Redis\RedisScripts;

use BrainExe\Core\Redis\RedisScripts;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Redis\RedisScripts
 */
class RedisScriptsTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RedisScripts
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new RedisScripts();
	}

	public function testGetSha1() {
		$name = 'name';
		$sha1 = 'sha1';
		$script = 'script';
		$this->_subject->registerScript($name, $sha1, $script);

		$actual_result = $this->_subject->getSha1($name);
		$this->assertEquals($sha1, $actual_result);
	}

	public function testGetAllScripts() {
		$name = 'name';
		$sha1 = 'sha1';
		$script = 'script';
		$this->_subject->registerScript($name, $sha1, $script);

		$actual_result = $this->_subject->getAllScripts($name);
		$this->assertEquals([$sha1 => $script], $actual_result);
	}

}
