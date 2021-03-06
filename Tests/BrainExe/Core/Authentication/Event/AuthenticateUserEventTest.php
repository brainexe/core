<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\AuthenticationDataVO;
use BrainExe\Core\Authentication\Event\AuthenticateUserEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BrainExe\Core\Authentication\Event\AuthenticateUserEvent
 */
class AuthenticateUserEventTest extends TestCase
{

    public function testEvent()
    {
        /** @var AuthenticationDataVO $auth */
        $auth      = $this->createMock(AuthenticationDataVO::class);
        $eventName = 'eventName';

        $subject = new AuthenticateUserEvent($auth, $eventName);

        $this->assertEquals($auth, $subject->getAuthenticationData());
        $this->assertEquals($eventName, $subject->getEventName());
    }
}
