<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\AuthenticationDataVO;
use BrainExe\Core\Authentication\Event\AuthenticateUserEvent;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Authentication\Event\AuthenticateUserEvent
 */
class AuthenticateUserEventTest extends TestCase
{

    public function testEvent()
    {
        /** @var AuthenticationDataVO $auth */
        $auth      = $this->getMock(AuthenticationDataVO::class, [], [], '', false);
        $eventName = 'eventName';

        $subject = new AuthenticateUserEvent($auth, $eventName);

        $this->assertEquals($auth, $subject->getAuthenticationData());
        $this->assertEquals($eventName, $subject->event_name);
    }
}
