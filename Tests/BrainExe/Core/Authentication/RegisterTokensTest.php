<?php

namespace Tests\BrainExe\Core\Authentication\RegisterTokens;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\Core\Authentication\RegisterTokens;
use Redis;
use BrainExe\Core\Util\IdGenerator;

/**
 * @Covers BrainExe\Core\Authentication\RegisterTokens
 */
class RegisterTokensTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RegisterTokens
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;

	/**
	 * @var IdGenerator|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockIdGenerator;


	public function setUp() {
		parent::setUp();

		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

		$this->_subject = new RegisterTokens();
		$this->_subject->setRedis($this->_mockRedis);
		$this->_subject->setIdGenerator($this->_mockIdGenerator);
	}

	public function testAddToken() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->addToken();
	}

	public function testFetchToken() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->fetchToken($token);
	}

}
