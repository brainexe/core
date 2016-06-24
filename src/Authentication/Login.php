<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\Event\AuthenticateUserEvent;
use BrainExe\Core\Authentication\Exception\UserNotFoundException;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Service(public=false)
 */
class Login
{
    const TOKEN_LOGIN = 'login';

    use EventDispatcherTrait;

    /**
     * @var LoadUser
     */
    private $loadUser;

    /**
     * @var Token
     */
    private $token;

    /**
     * @var PasswordHasher
     */
    private $passwordHasher;

    /**
     * @Inject({
     *     "@Core.Authentication.LoadUser",
     *     "@Core.Authentication.Token",
     *     "@Core.Authentication.PasswordHasher"
     * })
     * @param LoadUser $userProvider
     * @param Token $token
     * @param PasswordHasher $passwordHasher
     */
    public function __construct(
        LoadUser $userProvider,
        Token $token,
        PasswordHasher $passwordHasher
    ) {
        $this->loadUser       = $userProvider;
        $this->token          = $token;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $oneTimeToken
     * @param SessionInterface $session
     * @throws UserException
     * @return UserVO
     */
    public function tryLogin(
        string $username,
        string $password,
        string $oneTimeToken,
        SessionInterface $session
    ) : UserVO {
        $userVo = $this->loadUser->loadUserByUsername($username);

        if (!$this->passwordHasher->verifyHash($password, $userVo->getPassword())) {
            throw new UserException('Invalid Password');
        }

        $authenticationVo = new AuthenticationDataVO($userVo, $password, $oneTimeToken);

        $this->handleLogin($session, $authenticationVo, $userVo);

        return $userVo;
    }

    /**
     * @param string $token
     * @param SessionInterface $session
     * @return UserVO
     * @throws UserException
     */
    public function loginWithToken(string $token, SessionInterface $session) : UserVO
    {
        $tokenData = $this->token->getToken($token);

        if (empty($tokenData) || !in_array(self::TOKEN_LOGIN, $tokenData['roles'])) {
            throw new UserException('Invalid Token');
        }

        $userVo = $this->loadUser->loadUserById($tokenData['userId']);

        $authenticationVo = new AuthenticationDataVO($userVo, null, null);

        $this->handleLogin($session, $authenticationVo, $userVo);

        return $userVo;
    }

    /**
     * @param string $username
     * @return bool
     */
    public function needsOneTimeToken(string $username) : bool
    {
        try {
            $user = $this->loadUser->loadUserByUsername($username);
        } catch (UserNotFoundException $e) {
            return false;
        }

        return !empty($user->one_time_secret);
    }

    /**
     * @param SessionInterface $session
     * @param AuthenticationDataVO $authenticationVo
     * @param UserVO $userVo
     * @return AuthenticateUserEvent
     */
    private function handleLogin(
        SessionInterface $session,
        AuthenticationDataVO $authenticationVo,
        UserVO $userVo
    ) {
        $event = new AuthenticateUserEvent($authenticationVo, AuthenticateUserEvent::CHECK);
        $this->dispatchEvent($event);

        $session->set('user_id', $userVo->id);

        // renew session-id to prevent session fixation
        $session->migrate();

        $event = new AuthenticateUserEvent(
            $authenticationVo,
            AuthenticateUserEvent::AUTHENTICATED
        );

        $this->dispatchEvent($event);
    }
}
