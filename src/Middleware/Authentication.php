<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\AnonymusUserVO;

use BrainExe\Core\Authentication\LoadUser;
use BrainExe\Core\Authentication\UserVO;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Route;

/**
 * @Middleware("Middleware.Authentication")
 */
class Authentication extends AbstractMiddleware
{
    /**
     * @var bool
     */
    private $guestsAllowed;

    /**
     * @var LoadUser
     */
    private $loadUser;

    /**
     * @Inject({
     *  "%application.guests_allowed%",
     *  "@Authentication.LoadUser",
     * })
     * @param boolean $guestsAllowed
     * @param LoadUser $loadUser
     */
    public function __construct($guestsAllowed, LoadUser $loadUser)
    {
        $this->guestsAllowed = $guestsAllowed;
        $this->loadUser      = $loadUser;
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route)
    {
        $session = $request->getSession();
        $userId  = $session->get('user_id');

        $user = $this->loadUser($userId);

        $request->attributes->set('user', $user);
        $request->attributes->set('user_id', $userId);

        $this->checkForRole($route, $user);

        if ($this->guestsAllowed || $route->hasDefault('_guest')) {
            return null;
        }

        if (!$userId) {
            if ($request->isXmlHttpRequest()) {
                throw new UserException(gettext('Not logged in'));
            }
            return new RedirectResponse('#/login');
        }

        return null;
    }

    /**
     * @param Route $route
     * @param UserVO $user
     * @throws MethodNotAllowedException
     */
    protected function checkForRole(Route $route, UserVO $user)
    {
        if ($route->hasDefault('_role')) {
            $role = $route->getDefault('_role');
            if (!in_array($role, $user->roles)) {
                throw new MethodNotAllowedException([]);
            }
        }
    }

    /**
     * @param int $userId
     * @return AnonymusUserVO|UserVO
     */
    private function loadUser($userId)
    {
        if ($userId > 0) {
            return $this->loadUser->loadUserById($userId);
        } else {
            return new AnonymusUserVO();
        }
    }
}
