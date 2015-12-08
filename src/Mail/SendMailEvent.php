<?php

namespace BrainExe\Core\Mail;

use BrainExe\Core\EventDispatcher\AbstractEvent;

/**
 * @api
 */
class SendMailEvent extends AbstractEvent
{

    const TYPE = 'email.send';

    /**
     * @var string
     */
    private $recipient;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body;

    /**
     * @param string $recipient
     * @param string $subject
     * @param string $body
     */
    public function __construct($recipient, $subject, $body)
    {
        parent::__construct(self::TYPE);

        $this->recipient = $recipient;
        $this->subject   = $subject;
        $this->body      = $body;
    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}
