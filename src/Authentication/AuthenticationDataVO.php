<?php

namespace BrainExe\Core\Authentication;

class AuthenticationDataVO
{
    /**
     * @var UserVO
     */
    private $userVo;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $oneTimeToken;

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

    /**
     * @return UserVO
     */
    public function getUser() : UserVO
    {
        return $this->userVo;
    }

    /**
     * @return string
     */
    public function getOneTimeToken()
    {
        return $this->oneTimeToken;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
