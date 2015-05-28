<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\AnonymusUserVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class LogoutController
{

    /**
     * @param Request $request
     * @return AnonymusUserVO
     * @Route("/logout/", name="user.logout")
     */
    public function logout(Request $request)
    {
        $request->getSession()->set('user_id', null);
        $request->getSession()->set('user', null);

        return new AnonymusUserVO();
    }
}
