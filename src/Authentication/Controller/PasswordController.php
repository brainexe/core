<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\UserVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller("Authentication.PasswordController")
 */
class PasswordController
{

    /**
     * @var DatabaseUserProvider
     */
    private $user;

    /**
     * @Inject("@DatabaseUserProvider")
     * @param DatabaseUserProvider $userProvider
     */
    public function __construct(DatabaseUserProvider $userProvider)
    {
        $this->user = $userProvider;
    }

    /**
     * @param Request $request
     * @return boolean
     * @Route("/user/change_password/", name="user.change_password", methods="POST")
     */
    public function changePassword(Request $request)
    {
        $oldPassword = $request->request->get('oldPassword');

        // todo verify old password

        $newPassword = $request->request->get('newPassword');
        /** @var UserVO $user */
        $user = $request->attributes->get('user');
        $this->user->changePassword($user, $newPassword);

        return true;
    }
}
