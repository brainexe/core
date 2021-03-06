<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\Register;
use BrainExe\Core\Authentication\UserVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class RegisterController
{

    /**
     * @var Register
     */
    private $register;

    /**
     * @param Register $register
     */
    public function __construct(Register $register)
    {
        $this->register = $register;
    }

    /**
     * @param Request $request
     * @return UserVO
     * @Route("/register/", name="authenticate.doRegister", methods="POST")
     * @Guest
     */
    public function register(Request $request) : UserVO
    {
        $username       = $request->request->get('username');
        $plainPassword  = $request->request->get('password');
        $token          = $request->cookies->get('token');

        $user           = new UserVO();
        $user->username = $username;
        $user->password = $plainPassword;

        $this->register->registerUser($user, $request->getSession(), $token);

        return $user;
    }
}
