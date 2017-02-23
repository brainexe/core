<?php

namespace BrainExe\Core\Authentication\TOTP;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\Exception\UserNotFoundException;
use BrainExe\Core\Authentication\UserProvider;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Mail\SendMailEvent;
use BrainExe\Core\Util\IdGenerator;

/**
 * @Service
 */
class OneTimePassword
{

    const SECRET_LENGTH = 16;

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var TOTP
     */
    private $totp;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var IdGenerator
     */
    private $idGenerator;

    /**
     * @param UserProvider $databaseUserProvider
     * @param TOTP $totp
     * @param EventDispatcher $dispatcher
     * @param IdGenerator $idGenerator
     */
    public function __construct(
        UserProvider $databaseUserProvider,
        TOTP $totp,
        EventDispatcher $dispatcher,
        IdGenerator $idGenerator
    ) {
        $this->userProvider = $databaseUserProvider;
        $this->totp         = $totp;
        $this->dispatcher   = $dispatcher;
        $this->idGenerator  = $idGenerator;
    }

    /**
     * @param UserVO $userVo
     * @return Data
     */
    public function generateSecret(UserVO $userVo)
    {
        $secret = $this->idGenerator->generateRandomId(self::SECRET_LENGTH);

        $userVo->one_time_secret = $secret;
        $this->userProvider->setUserProperty($userVo, 'one_time_secret');

        return $this->getData($secret);
    }

    /**
     * @param $secret
     * @return Data
     */
    public function getData($secret)
    {
        $url = $this->totp->getUri($secret);

        $data = new Data();
        $data->secret = $secret;
        $data->url    = $url;

        return $data;
    }

    /**
     * @param UserVO $userVo
     * @param string $givenToken
     * @throws UserException
     */
    public function verifyOneTimePassword(UserVO $userVo, $givenToken)
    {
        if (empty($userVo->one_time_secret)) {
            throw new UserException(_("No one time secret requested"));
        }

        if (empty($givenToken)) {
            throw new UserException(_("No one time token given"));
        }

        $verified = $this->totp->verify($userVo->one_time_secret, $givenToken);

        if (!$verified) {
            throw new UserException(sprintf(_('Invalid token: "%s"!'), $givenToken));
        }
    }

    /**
     * @param UserVO $userVo
     */
    public function deleteOneTimeSecret(UserVO $userVo)
    {
        $userVo->one_time_secret = null;

        $this->userProvider->setUserProperty($userVo, 'one_time_secret');
    }

    /**
     * @param string $userName
     * @throws UserException
     */
    public function sendCodeViaMail($userName)
    {
        try {
            $user = $this->userProvider->loadUserByUsername($userName);

            if (empty($user->email)) {
                throw new UserException(_('No email address defined for this user'));
            }
            $code = $this->totp->current($user->one_time_secret);

            $event = new SendMailEvent($user->email, $code, $code);
            $this->dispatcher->dispatchEvent($event);
        } catch (UserNotFoundException $e) {
            throw new UserException(_('Invalid username'));
        }
    }
}
