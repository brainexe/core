<?php

namespace Tests\BrainExe\Core\Authentication\Controller\RegisterController;

use BrainExe\Core\Authentication\Controller\RegisterController;
use BrainExe\Core\Authentication\Register;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers BrainExe\Core\Authentication\Controller\RegisterController
 */
class RegisterControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RegisterController
	 */
	private $_subject;

	/**
	 * @var Register|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRegister;

	public function setUp() {

		$this->_mockRegister = $this->getMock(Register::class, [], [], '', false);
		$this->_subject = new RegisterController($this->_mockRegister);

	}

	public function testDoRegister() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$actual_result = $this->_subject->doRegister($request);
	}

}
