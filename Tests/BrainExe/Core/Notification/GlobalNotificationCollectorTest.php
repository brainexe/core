<?php

namespace Tests\BrainExe\Core\Notification\GlobalNotificationCollector;

use BrainExe\Core\Notification\GlobalNotificationCollector;
use BrainExe\Core\Notification\NotificationCollectorInterface;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Notification\GlobalNotificationCollector
 */
class GlobalNotificationCollectorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var GlobalNotificationCollector
	 */
	private $_subject;


	public function setUp() {


		$this->_subject = new GlobalNotificationCollector();

	}

	public function testAddCollector() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$collector = new NotificationCollectorInterface();
		$this->_subject->addCollector($collector);
	}

	public function testGetNotification() {
		$this->markTestIncomplete('This is only a dummy implementation');


		$this->_subject->getNotification();
	}

}
