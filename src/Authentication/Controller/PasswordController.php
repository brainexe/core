<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\AddFlashTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class PasswordController implements ControllerInterface
{

    use AddFlashTrait;

    /**
     * @var DatabaseUserProvider
     */
    private $userProvider;

    /**
     * @inject("@DatabaseUserProvider")
     * @param DatabaseUserProvider $userProvider
     */
    public function __construct(DatabaseUserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param Request $request
     * @return boolean
     * @Route("/user/change_password/", name="user.change_password", methods="POST")
     */
    public function changePassword(Request $request)
    {
        $password = $request->request->get('password');
        /** @var UserVO $user */
        $user = $request->attributes->get('user');

        $this->userProvider->changePassword($user, $password);

        return true;
    }
}
