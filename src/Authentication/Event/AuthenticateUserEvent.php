<?php

namespace BrainExe\Core\Authentication\Event;

use BrainExe\Core\Authentication\AuthenticationDataVO;
use BrainExe\Core\EventDispatcher\AbstractEvent;

/**
 * @api
 */
class AuthenticateUserEvent extends AbstractEvent
{

    const CHECK         = 'authenticate.check';
    const AUTHENTICATED = 'authenticate.authenticated';
    const FAILED        = 'authenticate.failed';

    /**
     * @var AuthenticationDataVO
     */
    private $authenticationData;

    /**
     * @param AuthenticationDataVO $authentication
     * @param string $eventName
     */
    public function __construct(AuthenticationDataVO $authentication, $eventName)
    {
        parent::__construct($eventName);

        $this->authenticationData = $authentication;
    }

    /**
     * @return AuthenticationDataVO
     */
    public function getAuthenticationData()
    {
        return $this->authenticationData;
    }
}
