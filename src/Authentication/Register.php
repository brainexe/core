<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Service
 */
class Register
{

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var RegisterTokens
     */
    private $registerTokens;

    /**
     * @var boolean
     */
    private $registrationEnabled;

    /**
     * @Inject({
     *     "registrationEnabled" = "%application.registration_enabled%"
     * })
     * @param UserProvider $userProvider
     * @param RegisterTokens $tokens
     * @param bool $registrationEnabled
     */
    public function __construct(
        UserProvider $userProvider,
        RegisterTokens $tokens,
        bool $registrationEnabled
    ) {
        $this->userProvider        = $userProvider;
        $this->registerTokens      = $tokens;
        $this->registrationEnabled = $registrationEnabled;
    }

    /**
     * @param UserVO $user
     * @param Session|SessionInterface $session
     * @param string $token
     * @throws UserException
     * @return int
     */
    public function registerUser(UserVO $user, Session $session, $token = null) : int
    {
        $this->checkInput($user);

        if (!$this->registrationEnabled
            && $token !== null
            && !$this->registerTokens->fetchToken($token)
        ) {
            throw new UserException('You have to provide a valid register token!');
        }

        $userId = $this->userProvider->register($user);

        $session->set('user', $user);

        return $userId;
    }

    /**
     * @param UserVO $user
     * @throws UserException
     */
    protected function checkInput(UserVO $user) : void
    {
        if (mb_strlen($user->username) <= 1) {
            throw new UserException('Username must not be empty');
        }

        if (mb_strlen($user->password) <= 1) {
            throw new UserException('Password must not be empty');
        }

        try {
            $this->userProvider->loadUserByUsername($user->getUsername());

            throw new UserException(sprintf('User %s already exists', $user->getUsername()));
        } catch (UserNotFoundException $e) {
            // all fine
        }
    }
}
