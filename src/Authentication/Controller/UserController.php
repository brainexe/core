<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\UserProvider;
use BrainExe\Core\Authentication\UserVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class UserController
{
    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @Inject("@Core.Authentication.UserProvider")
     * @param UserProvider $userProvider
     */
    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param Request $request
     * @return UserVO
     * @Route("/user/", name="authenticate.current_user", methods="GET")
     * @Guest
     */
    public function getCurrentUser(Request $request) : UserVO
    {
        return $request->attributes->get('user');
    }

    /**
     * @return string[]
     * @Route("/user/avatar/", name="authenticate.avatars", methods="GET", options={"cache":10800})
     */
    public function getAvatars() : array
    {
        return UserVO::AVATARS;
    }

    /**
     * @param Request $request
     * @param string $avatar
     * @return UserVO
     * @throws UserException
     * @Route("/user/avatar/{avatar}/", name="authenticate.setAvatar", methods="POST")
     */
    public function setAvatars(Request $request, $avatar) : UserVO
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
     * @param Request $request
     * @return UserVO
     * @throws UserException
     * @Route("/user/change_email/", name="authenticate.setEmail", methods="POST")
     * @Guest
     */
    public function setEmail(Request $request) : UserVO
    {
        /** @var UserVO $user */
        $user = $request->attributes->get('user');
        $user->email = $request->request->get('email');
        $this->userProvider->setUserProperty($user, UserVO::PROPERTY_EMAIL);

        return $user;
    }

    /**
     * Receives a list of all registered user names. indexed by user-id
     *
     * @return string[]
     * @Route("/user/list/", name="authenticate.list_user", methods="GET", options={"cache":30})
     */
    public function getList() : array
    {
        return array_flip($this->userProvider->getAllUserNames());
    }
}
