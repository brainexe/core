<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\UserVO;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Route;

/**
 * @Middleware("Middleware.Authentication", priority=8)
 */
class Authentication extends AbstractMiddleware
{

    /**
     * @var bool
     */
    private $guestsAllowed;

    /**
     * @var DatabaseUserProvider
     */
    private $userProvider;

    /**
     * @var bool
     */
    private $allowedPrivateIps;

    /**
     * @Inject({
     *  "%application.guests_allowed%",
     *  "@DatabaseUserProvider",
     * })
     * @param boolean $guestsAllowed
     * @param DatabaseUserProvider $userProvider
     */
    public function __construct($guestsAllowed, DatabaseUserProvider $userProvider)
    {
        $this->guestsAllowed     = $guestsAllowed;
        $this->userProvider      = $userProvider;
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
        $session   = $request->getSession();
        $userId    = $session->get('user_id');

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
    protected function checkForRole(Route $route, $user)
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
            $user = $this->userProvider->loadUserById($userId);
            return $user;
        } else {
            $user = new AnonymusUserVO();
            return $user;
        }
    }
}
