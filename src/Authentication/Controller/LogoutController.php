<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\AnonymusUserVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller("Authentication.LogoutController")
 */
class LogoutController
{

    /**
     * @param Request $request
     * @return AnonymusUserVO
     * @Route("/logout/", name="user.logout", methods="POST")
     * @Guest
     */
    public function logout(Request $request) : AnonymusUserVO
    {
        $request->getSession()->clear();

        return new AnonymusUserVO();
    }
}
