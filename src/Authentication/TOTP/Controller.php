<?php

namespace BrainExe\Core\Authentication\TOTP;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\UserVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation
 */
class Controller
{

    /**
     * @var OneTimePassword
     */
    private $oneTimePassword;

    /**
     * @param OneTimePassword $oneTimePassword
     */
    public function __construct(OneTimePassword $oneTimePassword)
    {
        $this->oneTimePassword = $oneTimePassword;
    }

    /**
     * @param Request $request
     * @return Data
     * @Route("/one_time_password/", name="user.get_one_time_secret", methods="GET")
     */
    public function getOneTimeSecret(Request $request)
    {
        /** @var UserVO $user */
        $userVo = $request->attributes->get('user');

        if ($userVo->one_time_secret) {
            $secretData = $this->oneTimePassword->getData($userVo->one_time_secret);
        } else {
            $secretData = null;
        }

        return $secretData;
    }

    /**
     * @param Request $request
     * @return Data
     * @Route("/one_time_password/request/", name="user.request_one_time_secret", methods="POST")
     */
    public function requestOneTimeSecret(Request $request) : Data
    {
        /** @var UserVO $user */
        $userVo = $request->attributes->get('user');
        $newToken = (bool)$request->request->get('new');

        if (!$userVo->one_time_secret || $newToken) {
            $secretData = $this->oneTimePassword->generateSecret($userVo);
        } else {
            $secretData = $this->oneTimePassword->getData($userVo->one_time_secret);
        }

        return $secretData;
    }

    /**
     * @param Request $request
     * @return bool
     * @Route("/one_time_password/", name="user.delete_one_time_secret", methods="DELETE")
     */
    public function deleteOneTimeSecret(Request $request) : bool
    {
        /** @var UserVO $user */
        $userVo = $request->attributes->get('user');

        $this->oneTimePassword->deleteOneTimeSecret($userVo);

        return true;
    }

    /**
     * @param Request $request
     * @return bool
     * @Route("/one_time_password/mail/", name="authenticate.send_otp_mil", methods="POST")
     * @Guest
     */
    public function sendCodeViaMail(Request $request) : bool
    {
        $userName = $request->request->get('user_name');

        $this->oneTimePassword->sendCodeViaMail($userName);

        return true;
    }
}
