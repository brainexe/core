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
     * @param UserVO $userVo
     * @param string $eventName
     */
    public function __construct(UserVO $userVo, $eventName)
    {
        parent::__construct($eventName);

        $this->userVo = $userVo;
    }

    /**
     * @return UserVO
     */
    public function getUserVO()
    {
        return $this->userVo;
    }
}
