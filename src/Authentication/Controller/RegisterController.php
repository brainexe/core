<?php

namespace BrainExe\Core\Authentication\Controller;

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
     * @inject("@Register")
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

        $user_vo           = new UserVO();
        $user_vo->username = $username;
        $user_vo->password = $plainPassword;

        $this->register->register($user_vo, $request->getSession(), $token);

        $response = new JsonResponse($user_vo);
        $this->_addFlash($response, self::ALERT_SUCCESS, sprintf('Welcome %s', $user_vo->username));

        return $response;
    }
}
