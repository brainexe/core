<?php

namespace Tests\BrainExe\Core\Notification\GlobalNotificationCollector;

use BrainExe\Core\Notification\GlobalNotificationCollector;
use BrainExe\Core\Notification\NotificationCollectorInterface;
use PHPUnit_Framework_MockObject_MockObject;
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

	public function testGetNotifications() {
		/** @var NotificationCollectorInterface|PHPUnit_Framework_MockObject_MockObject $collector1 */
		$collector1 = $this->getMock(NotificationCollectorInterface::class);

		/** @var NotificationCollectorInterface|PHPUnit_Framework_MockObject_MockObject $collector2 */
		$collector2 = $this->getMock(NotificationCollectorInterface::class);

		$notifications_1 = ['notifications'];

		$collector1
			->expects($this->once())
			->method('getNotification')
			->will($this->returnValue($notifications_1));

		$collector2
			->expects($this->once())
			->method('getNotification')
			->will($this->returnValue([]));

		$this->_subject->addCollector($collector1);
		$this->_subject->addCollector($collector2);

		$actual_result = $this->_subject->getNotification();

		$this->assertEquals($notifications_1, $actual_result);
	}

}
