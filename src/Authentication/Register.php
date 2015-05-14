<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\Exception\UsernameNotFoundException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Service(public=false)
 */
class Register
{

    /**
     * @var DatabaseUserProvider
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
     * @Inject({"@DatabaseUserProvider", "@RegisterTokens", "%application.registration_enabled%"})
     * @param DatabaseUserProvider $userProvider
     * @param RegisterTokens $tokens
     * @param $registrationEnabled
     */
    public function __construct(
        DatabaseUserProvider $userProvider,
        RegisterTokens $tokens,
        $registrationEnabled
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
     * @return integer
     */
    public function registerUser(UserVO $user, Session $session, $token = null)
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
    protected function checkInput(UserVO $user)
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
        } catch (UsernameNotFoundException $e) {
            // all fine
        }
    }
}
