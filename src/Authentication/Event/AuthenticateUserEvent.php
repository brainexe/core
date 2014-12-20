<?php

namespace BrainExe\Core\Authentication\Event;

use BrainExe\Core\Authentication\AuthenticationDataVO;
use BrainExe\Core\EventDispatcher\AbstractEvent;

class AuthenticateUserEvent extends AbstractEvent
{

    const CHECK = 'authenticate.check';
    const AUTHENTICATED = 'authenticate.authenticated';
    const FAILED = 'authenticate.failed';

    /**
     * @var AuthenticationDataVO
     */
    private $authentication_data;

    /**
     * @param AuthenticationDataVO $userVo
     * @param string $eventName
     */
    public function __construct(AuthenticationDataVO $userVo, $eventName)
    {
        parent::__construct($eventName);

        $this->authentication_data = $userVo;
    }

    /**
     * @return AuthenticationDataVO
     */
    public function getAuthenticationData()
    {
        return $this->authentication_data;
    }
}
