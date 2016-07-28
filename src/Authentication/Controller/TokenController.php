<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\Token;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller("Authentication.Controller.TokenController")
 */
class TokenController
{

    /**
     * @var Token
     */
    private $token;

    /**
     * @Inject("@Core.Authentication.Token")
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * @param Request $request
     * @Route("/user/tokens/", name="authenticate.tokens.get", methods="GET")
     * @return array
     */
    public function getTokens(Request $request) : array
    {
        $userId = $request->attributes->getInt('user_id');

        return iterator_to_array($this->token->getTokensForUser($userId));
    }

    /**
     * @param Request $request
     * @Route("/user/tokens/", name="authenticate.tokens.new", methods="POST")
     * @return string
     */
    public function addToken(Request $request) : string
    {
        $userId = $request->attributes->getInt('user_id');
        $roles  = (array)$request->request->get('roles');
        $name   = (string)$request->request->get('name');

        return $this->token->addToken($userId, $roles, $name);
    }

    /**
     * @param Request $request
     * @Route("/user/tokens/{token}/", name="authenticate.tokens.revoke", methods="DELETE")
     * @param string $token
     * @return bool
     */
    public function revoke(Request $request, string $token) : bool
    {
        unset($request);

        $this->token->revoke($token);

        return true;
    }
}
