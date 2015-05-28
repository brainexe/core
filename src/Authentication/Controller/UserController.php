<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\UserVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class UserController
{

    /**
     * @param Request $request
     * @return UserVO
     * @Route("/user/", name="authenticate.current_user", methods="GET")
     * @Guest
     */
    public function getCurrentUser(Request $request)
    {
        return $request->attributes->get('user');
    }
}
