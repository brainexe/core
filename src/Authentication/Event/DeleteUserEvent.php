<?php

namespace BrainExe\Core\Authentication\Event;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\EventDispatcher\AbstractEvent;

/**
 * @api
 */
class DeleteUserEvent extends AbstractEvent
{

    const DELETE = 'user.delete';

    /**
     * @var UserVO
     */
    private $userVo;

    /**
     * @param UserVO $authentication
     * @param string $eventName
     */
    public function __construct(UserVO $authentication, $eventName)
    {
        parent::__construct($eventName);

        $this->userVo = $authentication;
    }

    /**
     * @return UserVO
     */
    public function getUserVO()
    {
        return $this->userVo;
    }
}
