<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Controller\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class UserController implements ControllerInterface
{

    /**
     * @param Request $request
     * @return UserVO
     * @Route("/user/current/", name="authenticate.current_user")
     * @Guest
     */
    public function getCurrentUser(Request $request)
    {
        return $request->attributes->get('user');
    }
}
