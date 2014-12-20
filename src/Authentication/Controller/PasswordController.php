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
    private $_databaseUserProvider;

    /**
     * @inject("@DatabaseUserProvider")
     * @param DatabaseUserProvider $user_provider
     */
    public function __construct(DatabaseUserProvider $user_provider)
    {
        $this->_databaseUserProvider = $user_provider;
    }

    /**
     * @param Request $request
     * @return boolean
     * @Route("/user/change_password/", name="user.change_password", methods="POST")
     */
    public function changePassword(Request $request)
    {
        $new_password = $request->request->get('password');
        /** @var UserVO $user */
        $user = $request->attributes->get('user');

        $this->_databaseUserProvider->changePassword($user, $new_password);

        return true;
    }
}
