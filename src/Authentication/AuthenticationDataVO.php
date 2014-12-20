<?php

namespace BrainExe\Core\Authentication;

class AuthenticationDataVO
{
    /**
     * @var UserVO
     */
    public $user_vo;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $one_time_token;

    /**
     * @param UserVO $userVo
     * @param string $password
     * @param string $oneTimeToken
     */
    public function __construct(UserVO $userVo, $password, $oneTimeToken)
    {
        $this->user_vo        = $userVo;
        $this->password       = $password;
        $this->one_time_token = $oneTimeToken;
    }
}
