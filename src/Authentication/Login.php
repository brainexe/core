<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\Event\AuthenticateUserEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @Service(public=false)
 */
class Login
{

    use EventDispatcherTrait;

    /**
     * @var DatabaseUserProvider
     */
    private $userProvider;

    /**
     * @Inject("@DatabaseUserProvider")
     * @param DatabaseUserProvider $userProvider
     */
    public function __construct(DatabaseUserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $oneTimeToken
     * @param SessionInterface $session
     * @throws UserException
     * @return UserVO
     */
    public function tryLogin($username, $password, $oneTimeToken, SessionInterface $session)
    {
        $userVo = $this->userProvider->loadUserByUsername($username);
        if (empty($userVo)) {
            throw new UserException("Invalid Username");
        }

        if (!$this->userProvider->verifyHash($password, $userVo->getPassword())) {
            throw new UserException("Invalid Password");
        }

        $authenticationVo = new AuthenticationDataVO($userVo, $password, $oneTimeToken);

        $event = new AuthenticateUserEvent($authenticationVo, AuthenticateUserEvent::CHECK);
        $this->dispatchEvent($event);

        $session->set('user_id', $userVo->id);

        $event = new AuthenticateUserEvent($authenticationVo, AuthenticateUserEvent::AUTHENTICATED);
        $this->dispatchEvent($event);

        return $userVo;
    }

    /**
     * @param string $username
     * @return bool
     */
    public function needsOneTimeToken($username)
    {
        try {
            $user = $this->userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $e) {
            return false;
        }

        return !empty($user->one_time_secret);
    }
}
