<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\IP;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=8)
 */
class Authentication extends AbstractMiddleware
{

    /**
     * @var
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
     * @var IP
     */
    private $ip;

    /**
     * @Inject({
     *  "%application.guests_allowed%",
     *  "%application.allowed_private_ips%",
     *  "@DatabaseUserProvider",
     *  "@Authentication.IP"
     * })
     * @param boolean $guestsAllowed
     * @param $allowedPrivateIps
     * @param DatabaseUserProvider $userProvider
     * @param IP $ipCheck
     */
    public function __construct($guestsAllowed, $allowedPrivateIps, DatabaseUserProvider $userProvider, IP $ipCheck)
    {
        $this->guestsAllowed     = $guestsAllowed;
        $this->userProvider      = $userProvider;
        $this->allowedPrivateIps = $allowedPrivateIps;
        $this->ip                = $ipCheck;
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
    public function processRequest(Request $request, Route $route, $routeName)
    {
        $session   = $request->getSession();
        $userId    = $session->get('user_id');

        if (!$userId && $this->allowedPrivateIps && $this->ip->isLocalRequest($request)) {
            $userId = reset($this->userProvider->getAllUserNames());
        }

        $loggedIn  = $userId > 0;
        if ($loggedIn) {
            $user = $this->userProvider->loadUserById($userId);
        } else {
            $user = new AnonymusUserVO();
        }

        $request->attributes->set('user', $user);
        $request->attributes->set('user_id', $userId);

        if ($this->guestsAllowed) {
            return null;
        }

        if ($route->hasDefault('_guest')) {
            return null;
        }

        if ($route->hasDefault('_role')) {
            $role = $route->getDefault('_role');
            if (!in_array($role, $user->roles)) {
                throw new MethodNotAllowedException([]);
            }
        }

        if (!$loggedIn) {
            return new RedirectResponse('#/login');
        }

        return null;
    }
}
