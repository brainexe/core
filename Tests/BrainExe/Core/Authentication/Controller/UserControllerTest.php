<?php

namespace Tests\BrainExe\Core\Authentication\Controller\UserController;

use BrainExe\Core\Authentication\Controller\UserController;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Authentication\Controller\UserController
 */
class UserControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var UserController
	 */
	private $_subject;


	public function setUp() {


		$this->_subject = new UserController();

	}

	public function testGetCurrentUser() {
		$this->markTestIncomplete('This is only a dummy implementation');


		$actual_result = $this->_subject->getCurrentUser();
	}

}
