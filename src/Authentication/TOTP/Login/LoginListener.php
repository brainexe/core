<?php

namespace BrainExe\Core\Authentication\TOTP\Login;

use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\Event\AuthenticateUserEvent;
use BrainExe\Core\Authentication\TOTP\OneTimePassword;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class LoginListener implements EventSubscriberInterface
{
    /**
     * @var OneTimePassword
     */
    private $otp;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            AuthenticateUserEvent::CHECK => 'checkLogin'
        ];
    }

    /**
     * @param OneTimePassword $otp
     */
    public function __construct(OneTimePassword $otp)
    {
        $this->otp = $otp;
    }

    /**
     * @param AuthenticateUserEvent $event
     * @throws UserException
     */
    public function checkLogin(AuthenticateUserEvent $event)
    {
        $data    = $event->getAuthenticationData();
        $userVo  = $data->getUser();

        if (!empty($userVo->one_time_secret)) {
            $this->otp->verifyOneTimePassword($userVo, $data->getOneTimeToken());
        }
    }
}
