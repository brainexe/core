<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Middleware;

use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\Exception\UserNotFoundException;
use BrainExe\Core\Authentication\LoadUser;
use BrainExe\Core\Authentication\Token;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Translation\TranslationTrait;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Route;

/**
 * @Middleware("Middleware.TokenAuthentication")
 */
class TokenAuthentication extends AbstractMiddleware
{
    use TranslationTrait;

    /**
     * @var LoadUser
     */
    private $loadUser;

    /**
     * @var Token
     */
    private $token;

    /**
     * @param LoadUser $loadUser
     * @param Token $token
     */
    public function __construct(LoadUser $loadUser, Token $token)
    {
        $this->loadUser = $loadUser;
        $this->token    = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route)
    {
        $token = $request->get('accessToken');

        if (empty($token)) {
            return null;
        }

        $userId = $this->token->hasUserForRole($token);
        if (empty($userId)) {
            return null;
        }

        $user = $this->loadUser($userId);

        $request->attributes->set('user', $user);
    }

    /**
     * @todo check/implement
     * @param Route $route
     * @param UserVO $user
     * @throws MethodNotAllowedException
     */
    protected function checkForRole(Route $route, UserVO $user)
    {
        if ($route->hasDefault('_role')) {
            $role = $route->getDefault('_role');
            if (!in_array($role, $user->roles, true)) {
                throw new MethodNotAllowedException([], sprintf('Need role %s', $role));
            }
        }
    }

    /**
     * @param int $userId
     * @return AnonymusUserVO|UserVO
     */
    private function loadUser(int $userId) : UserVO
    {
        if ($userId > 0) {
            try {
                return $this->loadUser->loadUserById($userId);
            } catch (UserNotFoundException $e) {
                return new AnonymusUserVO();
            }
        } else {
            return new AnonymusUserVO();
        }
    }
}
