<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\PasswordHasher;
use BrainExe\Core\Authentication\UserProvider;
use BrainExe\Core\Authentication\UserVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller("Authentication.PasswordController")
 */
class PasswordController
{

    /**
     * @var UserProvider
     */
    private $user;
    /**
     * @var PasswordHasher
     */
    private $passwordHasher;

    /**
     * @Inject({
     *     "@Core.Authentication.UserProvider",
     *     "@Core.Authentication.PasswordHasher"
     * })
     * @param UserProvider $userProvider
     * @param PasswordHasher $passwordHasher
     */
    public function __construct(
        UserProvider $userProvider,
        PasswordHasher $passwordHasher
    ) {
        $this->user           = $userProvider;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param Request $request
     * @return bool
     * @throws UserException
     * @Route("/user/change_password/", name="user.change_password", methods="POST")
     */
    public function changePassword(Request $request) : bool
    {
        $oldPassword = $request->request->get('oldPassword');
        $newPassword = $request->request->get('newPassword');

        /** @var UserVO $user */
        $user = $request->attributes->get('user');
        if (!$this->passwordHasher->verifyHash($oldPassword, $user->getPassword())) {
            throw new UserException('Invalid Password given');
        }

        $this->user->changePassword($user, $newPassword);

        return true;
    }
}
