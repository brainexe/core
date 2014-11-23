<?php

namespace Tests\BrainExe\Core\Authentication\RegisterTokens;

use BrainExe\Core\Authentication\RegisterTokens;
use BrainExe\Core\Util\IdGenerator;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Redis;

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
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

		$this->_subject = new RegisterTokens();
		$this->_subject->setRedis($this->_mockRedis);
		$this->_subject->setIdGenerator($this->_mockIdGenerator);
	}

	public function testAddToken() {
		$id = 11880;

		$this->_mockIdGenerator
			->expects($this->once())
			->method('generateRandomId')
			->will($this->returnValue($id));

		$this->_mockRedis
			->expects($this->once())
			->method('sAdd')
			->with(RegisterTokens::TOKEN_KEY, $id);

		$actual_result = $this->_subject->addToken();

		$this->assertEquals($id, $actual_result);
	}

	public function testFetchToken() {
		$token = 11880;

		$this->_mockRedis
			->expects($this->once())
			->method('sRem')
			->with(RegisterTokens::TOKEN_KEY, $token)
			->will($this->returnValue(true));

		$actual_result = $this->_subject->fetchToken($token);

		$this->assertTrue($actual_result);
	}

}
