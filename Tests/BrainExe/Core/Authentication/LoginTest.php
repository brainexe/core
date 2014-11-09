<?php

namespace Tests\BrainExe\Core\Authentication\Login;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\Core\Authentication\Login;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\DependencyInjection\ObjectFinder;

/**
 * @Covers BrainExe\Core\Authentication\Login
 */
class LoginTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Login
	 */
	private $_subject;

	/**
	 * @var DatabaseUserProvider|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockDatabaseUserProvider;

	/**
	 * @var ObjectFinder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockObjectFinder;


	public function setUp() {
		parent::setUp();

		$this->_mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);
		$this->_mockObjectFinder = $this->getMock(ObjectFinder::class, [], [], '', false);

		$this->_subject = new Login($this->_mockDatabaseUserProvider);
		$this->_subject->setObjectFinder($this->_mockObjectFinder);
	}

	public function testTryLogin() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->tryLogin($username, $password, $one_time_token, $session);
	}

}
