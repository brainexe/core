<?php

namespace Matze\Tests\Core\Application;

use Matze\Core\Application\RedisSessionHandler;
use PHPUnit_Framework_TestCase;

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