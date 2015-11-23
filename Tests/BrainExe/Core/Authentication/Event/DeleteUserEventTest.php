<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\Event\DeleteUserEvent;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Authentication\Event\DeleteUserEvent
 */
class DeleteUserEventTest extends TestCase
{

    public function testEvent()
    {
        $user = new UserVO();
        $eventName = 'eventName';

        $subject = new DeleteUserEvent($user, $eventName);

        $this->assertEquals($user, $subject->getUserVO());
        $this->assertEquals($eventName, $subject->eventName);
    }
}
