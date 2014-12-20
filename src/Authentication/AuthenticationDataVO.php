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
     * @param UserVO $user_vo
     * @param string $password
     * @param string $one_time_token
     */
    function __construct(UserVO $user_vo, $password, $one_time_token)
    {
        $this->user_vo        = $user_vo;
        $this->password       = $password;
        $this->one_time_token = $one_time_token;
    }
}
