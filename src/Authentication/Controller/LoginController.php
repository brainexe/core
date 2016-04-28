<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\Login;
use BrainExe\Core\Authentication\UserVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller("Authentication.LoginController")
 */
class LoginController
{

    /**
     * @var Login
     */
    private $login;

    /**
     * @Inject("@Login")
     * @param Login $login
     */
    public function __construct(Login $login)
    {
        $this->login = $login;
    }

    /**
     * @param Request $request
     * @return UserVO
     * @Route("/login/", name="authenticate.doLogin", methods="POST")
     * @Guest
     */
    public function login(Request $request) : UserVO
    {
        $username      = $request->request->get('username');
        $plainPassword = $request->request->get('password');
        $oneTimeToken  = $request->request->getAlnum('one_time_token');

        $user = $this->login->tryLogin(
            $username,
            $plainPassword,
            $oneTimeToken,
            $request->getSession()
        );

        return $user;
    }

    /**
     * @param Request $request
     * @Route("/login/needsOneTimeToken", name="authenticate.needsOneTimeToken", methods="GET")
     * @return bool
     * @Guest
     */
    public function needsOneTimeToken(Request $request) : bool
    {
        $username = $request->query->get('username');

        return $this->login->needsOneTimeToken($username);
    }

    /**
     * @param Request $request
     * @Route("/login/token/{token}", name="authenticate.loginWithToken ", methods="GET")
     * @param string $token
     * @return UserVO|RedirectResponse
     * @throws UserException
     * @Guest
     */
    public function loginWithToken(Request $request, string $token)
    {
        $result = $this->login->loginWithToken($token, $request->getSession());

        if ($request->isXmlHttpRequest()) {
            return $result;
        }

        return new RedirectResponse('/');
    }
}
