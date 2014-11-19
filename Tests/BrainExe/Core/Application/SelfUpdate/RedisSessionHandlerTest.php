<?php

namespace BrainExe\Tests\Core\Application;

use BrainExe\Core\Application\RedisSessionHandler;
use PHPUnit_Framework_TestCase;

//TODO duplicate?
class RedisSessionHandlerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RedisSessionHandler
	 */
	private $_subject;

	public function setUp() {
		global $dic;
		$this->_subject = $dic->get('RedisSessionHandler');
	}

	public function testSession() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$payload = 'foobar';
		$session_id = '121212';

		$this->_subject->open(null, $session_id);

		$this->_subject->write($session_id, $payload);

		$this->assertEquals($payload, $this->_subject->read($session_id));

		$this->_subject->destroy($session_id);

		$this->assertEquals('', $this->_subject->read($session_id));

		$this->_subject->close();
		$this->_subject->gc(0);
	}
} 