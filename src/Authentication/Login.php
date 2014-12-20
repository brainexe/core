<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\Event\AuthenticateUserEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Service(public=false)
 */
class Login
{

    use EventDispatcherTrait;

    /**
     * @var DatabaseUserProvider
     */
    private $_userProvider;

    /**
     * @Inject("@DatabaseUserProvider")
     * @param DatabaseUserProvider $user_provider
     */
    public function __construct(DatabaseUserProvider $user_provider)
    {
        $this->_userProvider = $user_provider;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $one_time_token
     * @param SessionInterface $session
     * @throws UserException
     * @return UserVO
     */
    public function tryLogin($username, $password, $one_time_token, SessionInterface $session)
    {
        $user_vo = $this->_userProvider->loadUserByUsername($username);
        if (empty($user_vo)) {
            throw new UserException("Invalid Username");
        }

        if (!$this->_userProvider->verifyHash($password, $user_vo->getPassword())) {
            throw new UserException("Invalid Password");
        }

        $authentication_vo = new AuthenticationDataVO($user_vo, $password, $one_time_token);

        $event = new AuthenticateUserEvent($authentication_vo, AuthenticateUserEvent::CHECK);
        $this->dispatchEvent($event);

        $session->set('user_id', $user_vo->id);

        $event = new AuthenticateUserEvent($authentication_vo, AuthenticateUserEvent::AUTHENTICATED);
        $this->dispatchEvent($event);

        return $user_vo;
    }
}
