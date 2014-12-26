<?php

namespace Tests\BrainExe\Core\Notification\GlobalNotificationCollector;

use BrainExe\Core\Notification\GlobalNotificationCollector;
use BrainExe\Core\Notification\NotificationCollectorInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Notification\GlobalNotificationCollector
 */
class GlobalNotificationCollectorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var GlobalNotificationCollector
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new GlobalNotificationCollector();
    }

    public function testGetNotifications()
    {
        /** @var NotificationCollectorInterface|MockObject $collector1 */
        $collector1 = $this->getMock(NotificationCollectorInterface::class);

        /** @var NotificationCollectorInterface|MockObject $collector2 */
        $collector2 = $this->getMock(NotificationCollectorInterface::class);

        $notifications1 = ['notifications'];

        $collector1
            ->expects($this->once())
            ->method('getNotification')
            ->willReturn($notifications1);

        $collector2
            ->expects($this->once())
            ->method('getNotification')
            ->willReturn([]);

        $this->subject->addCollector($collector1);
        $this->subject->addCollector($collector2);

        $actualResult = $this->subject->getNotification();

        $this->assertEquals($notifications1, $actualResult);
    }
}
