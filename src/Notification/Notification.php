<?php

namespace BrainExe\Core\Notification;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use Monolog\Logger;

/**
 * @api
 */
class Notification extends AbstractEvent
{

    const NOTIFY = 'notification:notify';
    const RECIPIENT_SYSTEM = 0;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var int
     */
    private $level;

    /**
     * @var int
     */
    private $recipient;

    /**
     * @param string $message
     * @param string $subject
     * @param int $level
     * @param int $recipient
     */
    public function __construct(
        $message,
        $subject = '',
        $level = Logger::ALERT,
        $recipient = self::RECIPIENT_SYSTEM
    ) {
        parent::__construct(self::NOTIFY);

        $this->message   = $message;
        $this->subject   = $subject;
        $this->level     = $level;
        $this->recipient = $recipient;
    }

    /**
     * @return int
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
