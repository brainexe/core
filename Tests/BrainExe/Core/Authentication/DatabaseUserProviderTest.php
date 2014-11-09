<?php

namespace Tests\BrainExe\Core\Authentication\DatabaseUserProvider;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use Redis;
use BrainExe\Core\Util\IdGenerator;

/**
 * @Covers BrainExe\Core\Authentication\DatabaseUserProvider
 */
class DatabaseUserProviderTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var DatabaseUserProvider
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

		$this->_subject = new DatabaseUserProvider();
		$this->_subject->setRedis($this->_mockRedis);
		$this->_subject->setIdGenerator($this->_mockIdGenerator);
	}

	public function testLoadUserByUsername() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->loadUserByUsername($username);
	}

	public function testLoadUserById() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->loadUserById($user_id);
	}

	public function testGetAllUserNames() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getAllUserNames();
	}

	public function testRefreshUser() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->refreshUser($user);
	}

	public function testSupportsClass() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->supportsClass($class);
	}

	public function testGenerateHash() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->generateHash($password);
	}

	public function testVerifyHash() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->verifyHash($password, $hash);
	}

	public function testChangePassword() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->changePassword($user, $new_password);
	}

	public function testSetUserProperty() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->setUserProperty($user_vo, $property);
	}

	public function testRegister() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->register($user);
	}

}
