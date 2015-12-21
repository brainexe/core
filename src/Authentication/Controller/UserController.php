<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\UserVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class UserController
{
    /**
     * @var DatabaseUserProvider
     */
    private $userProvider;

    /**
     * @Inject("@DatabaseUserProvider")
     * @param DatabaseUserProvider $userProvider
     */
    public function __construct(DatabaseUserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param Request $request
     * @return UserVO
     * @Route("/user/", name="authenticate.current_user", methods="GET")
     * @Guest
     */
    public function getCurrentUser(Request $request)
    {
        return $request->attributes->get('user');
    }

    /**
     * @return string[]
     * @Route("/user/avatar/", name="authenticate.avatars", methods="GET")
     * @Guest
     */
    public function getAvatars()
    {
        return UserVO::AVATARS;
    }

    /**
     * @param Request $request
     * @param string $avatar
     * @return UserVO
     * @throws UserException
     * @Route("/user/avatar/{avatar}/", name="authenticate.setAvatar", methods="POST")
     * @Guest
     */
    public function setAvatars(Request $request, $avatar)
    {
        if (!in_array($avatar, UserVO::AVATARS)) {
            throw new UserException(sprintf(_('Invalid avatar: %s'), $avatar));
        }
        /** @var UserVO $user */
        $user = $request->attributes->get('user');
        $user->avatar = $avatar;
        $this->userProvider->setUserProperty($user, UserVO::PROPERTY_AVATAR);

        return $user;
    }

    /**
     * Receives a list all all registered users. indexed by user-id
     *
     * @return string[]
     * @Route("/user/list/", name="authenticate.list_user", methods="GET")
     */
    public function getList()
    {
        return array_flip($this->userProvider->getAllUserNames());
    }
}
