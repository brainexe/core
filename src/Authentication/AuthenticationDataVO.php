<?php

namespace BrainExe\Core\Authentication;

class AuthenticationDataVO
{
    /**
     * @var UserVO
     */
    public $userVo;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $oneTimeToken;

    /**
     * @param UserVO $userVo
     * @param string $password
     * @param string $oneTimeToken
     */
    public function __construct(UserVO $userVo, $password, $oneTimeToken)
    {
        $this->userVo       = $userVo;
        $this->password     = $password;
        $this->oneTimeToken = $oneTimeToken;
    }
}
