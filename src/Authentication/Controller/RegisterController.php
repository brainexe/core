<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\Register;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\AddFlashTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class RegisterController implements ControllerInterface
{

    use AddFlashTrait;

    /**
     * @var Register
     */
    private $register;

    /**
     * @Inject("@Register")
     * @param Register $register
     */
    public function __construct(Register $register)
    {
        $this->register = $register;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @Route("/register/", name="authenticate.doRegister", methods="POST")
     * @Guest
     */
    public function doRegister(Request $request)
    {
        $username       = $request->request->get('username');
        $plainPassword  = $request->request->get('password');
        $token          = $request->cookies->get('token');

        $user           = new UserVO();
        $user->username = $username;
        $user->password = $plainPassword;

        $this->register->registerUser($user, $request->getSession(), $token);

        $response = new JsonResponse($user);
        $this->addFlash($response, self::ALERT_SUCCESS, sprintf('Welcome %s', $user->username));

        return $response;
    }
}
