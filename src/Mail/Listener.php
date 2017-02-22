<?php

namespace BrainExe\Core\Mail;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use PHPMailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("Mail.Listener")
 */
class Listener implements EventSubscriberInterface
{

    /**
     * @var PHPMailer
     */
    private $mailer;

    /**
     * @Inject("@Mailer")
     * @param PHPMailer $mailer
     */
    public function __construct(PHPMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SendMailEvent::TYPE => 'sendMail'
        ];
    }

    /**
     * @param SendMailEvent $event
     */
    public function sendMail(SendMailEvent $event)
    {
        $this->mailer->Subject = $event->getSubject();
        $this->mailer->Body    = $event->getBody();
        $this->mailer->addAddress($event->getRecipient());
        $this->mailer->send();
    }
}
