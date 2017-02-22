<?php

namespace Tests\BrainExe\Core\Mail;

use BrainExe\Core\Mail\Listener;
use BrainExe\Core\Mail\SendMailEvent;
use PHPMailer;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

class ListenerTest extends TestCase
{

    /**
     * @var Listener
     */
    private $subject;

    /**
     * @var PHPMailer|MockObject
     */
    private $mailer;

    public function setup()
    {
        $this->mailer  = $this->createMock(PHPMailer::class);
        $this->subject = new Listener($this->mailer);
    }

    public function testGetListener()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testDispatchEvent()
    {
        $body      = 'body';
        $subject   = 'subject';
        $recipient = 'recipient';

        $event = new SendMailEvent($recipient, $subject, $body);

        $this->mailer
            ->expects($this->once())
            ->method('addAddress')
            ->with($recipient);
        $this->mailer
            ->expects($this->once())
            ->method('send');

        $this->subject->sendMail($event);
    }

    public function testEvent()
    {
        $body      = 'body';
        $subject   = 'subject';
        $recipient = 'recipient';

        $event = new SendMailEvent($recipient, $subject, $body);

        $this->assertEquals($body, $event->getBody());
        $this->assertEquals($subject, $event->getSubject());
        $this->assertEquals($recipient, $event->getRecipient());
    }
}
